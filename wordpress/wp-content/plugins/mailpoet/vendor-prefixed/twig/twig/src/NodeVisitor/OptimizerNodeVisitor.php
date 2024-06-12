<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\BlockReferenceNode;
use MailPoetVendor\Twig\Node\Expression\BlockReferenceExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\FilterExpression;
use MailPoetVendor\Twig\Node\Expression\FunctionExpression;
use MailPoetVendor\Twig\Node\Expression\GetAttrExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\Expression\ParentExpression;
use MailPoetVendor\Twig\Node\ForNode;
use MailPoetVendor\Twig\Node\IncludeNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\PrintNode;
final class OptimizerNodeVisitor implements NodeVisitorInterface
{
 public const OPTIMIZE_ALL = -1;
 public const OPTIMIZE_NONE = 0;
 public const OPTIMIZE_FOR = 2;
 public const OPTIMIZE_RAW_FILTER = 4;
 private $loops = [];
 private $loopsTargets = [];
 private $optimizers;
 public function __construct(int $optimizers = -1)
 {
 if ($optimizers > (self::OPTIMIZE_FOR | self::OPTIMIZE_RAW_FILTER)) {
 throw new \InvalidArgumentException(\sprintf('Optimizer mode "%s" is not valid.', $optimizers));
 }
 $this->optimizers = $optimizers;
 }
 public function enterNode(Node $node, Environment $env) : Node
 {
 if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
 $this->enterOptimizeFor($node, $env);
 }
 return $node;
 }
 public function leaveNode(Node $node, Environment $env) : ?Node
 {
 if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
 $this->leaveOptimizeFor($node, $env);
 }
 if (self::OPTIMIZE_RAW_FILTER === (self::OPTIMIZE_RAW_FILTER & $this->optimizers)) {
 $node = $this->optimizeRawFilter($node, $env);
 }
 $node = $this->optimizePrintNode($node, $env);
 return $node;
 }
 private function optimizePrintNode(Node $node, Environment $env) : Node
 {
 if (!$node instanceof PrintNode) {
 return $node;
 }
 $exprNode = $node->getNode('expr');
 if ($exprNode instanceof BlockReferenceExpression || $exprNode instanceof ParentExpression) {
 $exprNode->setAttribute('output', \true);
 return $exprNode;
 }
 return $node;
 }
 private function optimizeRawFilter(Node $node, Environment $env) : Node
 {
 if ($node instanceof FilterExpression && 'raw' == $node->getNode('filter')->getAttribute('value')) {
 return $node->getNode('node');
 }
 return $node;
 }
 private function enterOptimizeFor(Node $node, Environment $env) : void
 {
 if ($node instanceof ForNode) {
 // disable the loop variable by default
 $node->setAttribute('with_loop', \false);
 \array_unshift($this->loops, $node);
 \array_unshift($this->loopsTargets, $node->getNode('value_target')->getAttribute('name'));
 \array_unshift($this->loopsTargets, $node->getNode('key_target')->getAttribute('name'));
 } elseif (!$this->loops) {
 // we are outside a loop
 return;
 } elseif ($node instanceof NameExpression && 'loop' === $node->getAttribute('name')) {
 $node->setAttribute('always_defined', \true);
 $this->addLoopToCurrent();
 } elseif ($node instanceof NameExpression && \in_array($node->getAttribute('name'), $this->loopsTargets)) {
 $node->setAttribute('always_defined', \true);
 } elseif ($node instanceof BlockReferenceNode || $node instanceof BlockReferenceExpression) {
 $this->addLoopToCurrent();
 } elseif ($node instanceof IncludeNode && !$node->getAttribute('only')) {
 $this->addLoopToAll();
 } elseif ($node instanceof FunctionExpression && 'include' === $node->getAttribute('name') && (!$node->getNode('arguments')->hasNode('with_context') || \false !== $node->getNode('arguments')->getNode('with_context')->getAttribute('value'))) {
 $this->addLoopToAll();
 } elseif ($node instanceof GetAttrExpression && (!$node->getNode('attribute') instanceof ConstantExpression || 'parent' === $node->getNode('attribute')->getAttribute('value')) && (\true === $this->loops[0]->getAttribute('with_loop') || $node->getNode('node') instanceof NameExpression && 'loop' === $node->getNode('node')->getAttribute('name'))) {
 $this->addLoopToAll();
 }
 }
 private function leaveOptimizeFor(Node $node, Environment $env) : void
 {
 if ($node instanceof ForNode) {
 \array_shift($this->loops);
 \array_shift($this->loopsTargets);
 \array_shift($this->loopsTargets);
 }
 }
 private function addLoopToCurrent() : void
 {
 $this->loops[0]->setAttribute('with_loop', \true);
 }
 private function addLoopToAll() : void
 {
 foreach ($this->loops as $loop) {
 $loop->setAttribute('with_loop', \true);
 }
 }
 public function getPriority() : int
 {
 return 255;
 }
}
