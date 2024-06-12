<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Node;
if (!defined('ABSPATH')) exit;
class PseudoNode extends AbstractNode
{
 private $selector;
 private $identifier;
 public function __construct(NodeInterface $selector, string $identifier)
 {
 $this->selector = $selector;
 $this->identifier = \strtolower($identifier);
 }
 public function getSelector() : NodeInterface
 {
 return $this->selector;
 }
 public function getIdentifier() : string
 {
 return $this->identifier;
 }
 public function getSpecificity() : Specificity
 {
 return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
 }
 public function __toString() : string
 {
 return \sprintf('%s[%s:%s]', $this->getNodeName(), $this->selector, $this->identifier);
 }
}
