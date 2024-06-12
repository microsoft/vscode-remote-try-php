<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\CheckSecurityCallNode;
use MailPoetVendor\Twig\Node\CheckSecurityNode;
use MailPoetVendor\Twig\Node\CheckToStringNode;
use MailPoetVendor\Twig\Node\Expression\Binary\ConcatBinary;
use MailPoetVendor\Twig\Node\Expression\Binary\RangeBinary;
use MailPoetVendor\Twig\Node\Expression\FilterExpression;
use MailPoetVendor\Twig\Node\Expression\FunctionExpression;
use MailPoetVendor\Twig\Node\Expression\GetAttrExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\ModuleNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\PrintNode;
use MailPoetVendor\Twig\Node\SetNode;
final class SandboxNodeVisitor implements NodeVisitorInterface
{
 private $inAModule = \false;
 private $tags;
 private $filters;
 private $functions;
 private $needsToStringWrap = \false;
 public function enterNode(Node $node, Environment $env) : Node
 {
 if ($node instanceof ModuleNode) {
 $this->inAModule = \true;
 $this->tags = [];
 $this->filters = [];
 $this->functions = [];
 return $node;
 } elseif ($this->inAModule) {
 // look for tags
 if ($node->getNodeTag() && !isset($this->tags[$node->getNodeTag()])) {
 $this->tags[$node->getNodeTag()] = $node;
 }
 // look for filters
 if ($node instanceof FilterExpression && !isset($this->filters[$node->getNode('filter')->getAttribute('value')])) {
 $this->filters[$node->getNode('filter')->getAttribute('value')] = $node;
 }
 // look for functions
 if ($node instanceof FunctionExpression && !isset($this->functions[$node->getAttribute('name')])) {
 $this->functions[$node->getAttribute('name')] = $node;
 }
 // the .. operator is equivalent to the range() function
 if ($node instanceof RangeBinary && !isset($this->functions['range'])) {
 $this->functions['range'] = $node;
 }
 if ($node instanceof PrintNode) {
 $this->needsToStringWrap = \true;
 $this->wrapNode($node, 'expr');
 }
 if ($node instanceof SetNode && !$node->getAttribute('capture')) {
 $this->needsToStringWrap = \true;
 }
 // wrap outer nodes that can implicitly call __toString()
 if ($this->needsToStringWrap) {
 if ($node instanceof ConcatBinary) {
 $this->wrapNode($node, 'left');
 $this->wrapNode($node, 'right');
 }
 if ($node instanceof FilterExpression) {
 $this->wrapNode($node, 'node');
 $this->wrapArrayNode($node, 'arguments');
 }
 if ($node instanceof FunctionExpression) {
 $this->wrapArrayNode($node, 'arguments');
 }
 }
 }
 return $node;
 }
 public function leaveNode(Node $node, Environment $env) : ?Node
 {
 if ($node instanceof ModuleNode) {
 $this->inAModule = \false;
 $node->setNode('constructor_end', new Node([new CheckSecurityCallNode(), $node->getNode('constructor_end')]));
 $node->setNode('class_end', new Node([new CheckSecurityNode($this->filters, $this->tags, $this->functions), $node->getNode('class_end')]));
 } elseif ($this->inAModule) {
 if ($node instanceof PrintNode || $node instanceof SetNode) {
 $this->needsToStringWrap = \false;
 }
 }
 return $node;
 }
 private function wrapNode(Node $node, string $name) : void
 {
 $expr = $node->getNode($name);
 if ($expr instanceof NameExpression || $expr instanceof GetAttrExpression) {
 $node->setNode($name, new CheckToStringNode($expr));
 }
 }
 private function wrapArrayNode(Node $node, string $name) : void
 {
 $args = $node->getNode($name);
 foreach ($args as $name => $_) {
 $this->wrapNode($args, $name);
 }
 }
 public function getPriority() : int
 {
 return 0;
 }
}
