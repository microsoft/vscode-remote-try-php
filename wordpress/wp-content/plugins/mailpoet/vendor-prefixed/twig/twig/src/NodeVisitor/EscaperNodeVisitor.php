<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Extension\EscaperExtension;
use MailPoetVendor\Twig\Node\AutoEscapeNode;
use MailPoetVendor\Twig\Node\BlockNode;
use MailPoetVendor\Twig\Node\BlockReferenceNode;
use MailPoetVendor\Twig\Node\DoNode;
use MailPoetVendor\Twig\Node\Expression\ConditionalExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\FilterExpression;
use MailPoetVendor\Twig\Node\Expression\InlinePrint;
use MailPoetVendor\Twig\Node\ImportNode;
use MailPoetVendor\Twig\Node\ModuleNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\PrintNode;
use MailPoetVendor\Twig\NodeTraverser;
final class EscaperNodeVisitor implements NodeVisitorInterface
{
 private $statusStack = [];
 private $blocks = [];
 private $safeAnalysis;
 private $traverser;
 private $defaultStrategy = \false;
 private $safeVars = [];
 public function __construct()
 {
 $this->safeAnalysis = new SafeAnalysisNodeVisitor();
 }
 public function enterNode(Node $node, Environment $env) : Node
 {
 if ($node instanceof ModuleNode) {
 if ($env->hasExtension(EscaperExtension::class) && ($defaultStrategy = $env->getExtension(EscaperExtension::class)->getDefaultStrategy($node->getTemplateName()))) {
 $this->defaultStrategy = $defaultStrategy;
 }
 $this->safeVars = [];
 $this->blocks = [];
 } elseif ($node instanceof AutoEscapeNode) {
 $this->statusStack[] = $node->getAttribute('value');
 } elseif ($node instanceof BlockNode) {
 $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ? $this->blocks[$node->getAttribute('name')] : $this->needEscaping($env);
 } elseif ($node instanceof ImportNode) {
 $this->safeVars[] = $node->getNode('var')->getAttribute('name');
 }
 return $node;
 }
 public function leaveNode(Node $node, Environment $env) : ?Node
 {
 if ($node instanceof ModuleNode) {
 $this->defaultStrategy = \false;
 $this->safeVars = [];
 $this->blocks = [];
 } elseif ($node instanceof FilterExpression) {
 return $this->preEscapeFilterNode($node, $env);
 } elseif ($node instanceof PrintNode && \false !== ($type = $this->needEscaping($env))) {
 $expression = $node->getNode('expr');
 if ($expression instanceof ConditionalExpression && $this->shouldUnwrapConditional($expression, $env, $type)) {
 return new DoNode($this->unwrapConditional($expression, $env, $type), $expression->getTemplateLine());
 }
 return $this->escapePrintNode($node, $env, $type);
 }
 if ($node instanceof AutoEscapeNode || $node instanceof BlockNode) {
 \array_pop($this->statusStack);
 } elseif ($node instanceof BlockReferenceNode) {
 $this->blocks[$node->getAttribute('name')] = $this->needEscaping($env);
 }
 return $node;
 }
 private function shouldUnwrapConditional(ConditionalExpression $expression, Environment $env, string $type) : bool
 {
 $expr2Safe = $this->isSafeFor($type, $expression->getNode('expr2'), $env);
 $expr3Safe = $this->isSafeFor($type, $expression->getNode('expr3'), $env);
 return $expr2Safe !== $expr3Safe;
 }
 private function unwrapConditional(ConditionalExpression $expression, Environment $env, string $type) : ConditionalExpression
 {
 // convert "echo a ? b : c" to "a ? echo b : echo c" recursively
 $expr2 = $expression->getNode('expr2');
 if ($expr2 instanceof ConditionalExpression && $this->shouldUnwrapConditional($expr2, $env, $type)) {
 $expr2 = $this->unwrapConditional($expr2, $env, $type);
 } else {
 $expr2 = $this->escapeInlinePrintNode(new InlinePrint($expr2, $expr2->getTemplateLine()), $env, $type);
 }
 $expr3 = $expression->getNode('expr3');
 if ($expr3 instanceof ConditionalExpression && $this->shouldUnwrapConditional($expr3, $env, $type)) {
 $expr3 = $this->unwrapConditional($expr3, $env, $type);
 } else {
 $expr3 = $this->escapeInlinePrintNode(new InlinePrint($expr3, $expr3->getTemplateLine()), $env, $type);
 }
 return new ConditionalExpression($expression->getNode('expr1'), $expr2, $expr3, $expression->getTemplateLine());
 }
 private function escapeInlinePrintNode(InlinePrint $node, Environment $env, string $type) : Node
 {
 $expression = $node->getNode('node');
 if ($this->isSafeFor($type, $expression, $env)) {
 return $node;
 }
 return new InlinePrint($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
 }
 private function escapePrintNode(PrintNode $node, Environment $env, string $type) : Node
 {
 if (\false === $type) {
 return $node;
 }
 $expression = $node->getNode('expr');
 if ($this->isSafeFor($type, $expression, $env)) {
 return $node;
 }
 $class = \get_class($node);
 return new $class($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
 }
 private function preEscapeFilterNode(FilterExpression $filter, Environment $env) : FilterExpression
 {
 $name = $filter->getNode('filter')->getAttribute('value');
 $type = $env->getFilter($name)->getPreEscape();
 if (null === $type) {
 return $filter;
 }
 $node = $filter->getNode('node');
 if ($this->isSafeFor($type, $node, $env)) {
 return $filter;
 }
 $filter->setNode('node', $this->getEscaperFilter($type, $node));
 return $filter;
 }
 private function isSafeFor(string $type, Node $expression, Environment $env) : bool
 {
 $safe = $this->safeAnalysis->getSafe($expression);
 if (null === $safe) {
 if (null === $this->traverser) {
 $this->traverser = new NodeTraverser($env, [$this->safeAnalysis]);
 }
 $this->safeAnalysis->setSafeVars($this->safeVars);
 $this->traverser->traverse($expression);
 $safe = $this->safeAnalysis->getSafe($expression);
 }
 return \in_array($type, $safe) || \in_array('all', $safe);
 }
 private function needEscaping(Environment $env)
 {
 if (\count($this->statusStack)) {
 return $this->statusStack[\count($this->statusStack) - 1];
 }
 return $this->defaultStrategy ? $this->defaultStrategy : \false;
 }
 private function getEscaperFilter(string $type, Node $node) : FilterExpression
 {
 $line = $node->getTemplateLine();
 $name = new ConstantExpression('escape', $line);
 $args = new Node([new ConstantExpression($type, $line), new ConstantExpression(null, $line), new ConstantExpression(\true, $line)]);
 return new FilterExpression($node, $name, $args, $line);
 }
 public function getPriority() : int
 {
 return 0;
 }
}
