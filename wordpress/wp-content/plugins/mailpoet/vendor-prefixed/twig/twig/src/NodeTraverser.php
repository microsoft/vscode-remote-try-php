<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\NodeVisitor\NodeVisitorInterface;
final class NodeTraverser
{
 private $env;
 private $visitors = [];
 public function __construct(Environment $env, array $visitors = [])
 {
 $this->env = $env;
 foreach ($visitors as $visitor) {
 $this->addVisitor($visitor);
 }
 }
 public function addVisitor(NodeVisitorInterface $visitor) : void
 {
 $this->visitors[$visitor->getPriority()][] = $visitor;
 }
 public function traverse(Node $node) : Node
 {
 \ksort($this->visitors);
 foreach ($this->visitors as $visitors) {
 foreach ($visitors as $visitor) {
 $node = $this->traverseForVisitor($visitor, $node);
 }
 }
 return $node;
 }
 private function traverseForVisitor(NodeVisitorInterface $visitor, Node $node) : ?Node
 {
 $node = $visitor->enterNode($node, $this->env);
 foreach ($node as $k => $n) {
 if (null !== ($m = $this->traverseForVisitor($visitor, $n))) {
 if ($m !== $n) {
 $node->setNode($k, $m);
 }
 } else {
 $node->removeNode($k);
 }
 }
 return $visitor->leaveNode($node, $this->env);
 }
}
