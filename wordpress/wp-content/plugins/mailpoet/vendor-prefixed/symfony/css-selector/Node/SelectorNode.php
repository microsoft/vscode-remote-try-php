<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Node;
if (!defined('ABSPATH')) exit;
class SelectorNode extends AbstractNode
{
 private $tree;
 private $pseudoElement;
 public function __construct(NodeInterface $tree, ?string $pseudoElement = null)
 {
 $this->tree = $tree;
 $this->pseudoElement = $pseudoElement ? \strtolower($pseudoElement) : null;
 }
 public function getTree() : NodeInterface
 {
 return $this->tree;
 }
 public function getPseudoElement() : ?string
 {
 return $this->pseudoElement;
 }
 public function getSpecificity() : Specificity
 {
 return $this->tree->getSpecificity()->plus(new Specificity(0, 0, $this->pseudoElement ? 1 : 0));
 }
 public function __toString() : string
 {
 return \sprintf('%s[%s%s]', $this->getNodeName(), $this->tree, $this->pseudoElement ? '::' . $this->pseudoElement : '');
 }
}
