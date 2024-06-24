<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Node;
if (!defined('ABSPATH')) exit;
class ElementNode extends AbstractNode
{
 private $namespace;
 private $element;
 public function __construct(?string $namespace = null, ?string $element = null)
 {
 $this->namespace = $namespace;
 $this->element = $element;
 }
 public function getNamespace() : ?string
 {
 return $this->namespace;
 }
 public function getElement() : ?string
 {
 return $this->element;
 }
 public function getSpecificity() : Specificity
 {
 return new Specificity(0, 0, $this->element ? 1 : 0);
 }
 public function __toString() : string
 {
 $element = $this->element ?: '*';
 return \sprintf('%s[%s]', $this->getNodeName(), $this->namespace ? $this->namespace . '|' . $element : $element);
 }
}
