<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Node;
if (!defined('ABSPATH')) exit;
abstract class AbstractNode implements NodeInterface
{
 private $nodeName;
 public function getNodeName() : string
 {
 if (null === $this->nodeName) {
 $this->nodeName = \preg_replace('~.*\\\\([^\\\\]+)Node$~', '$1', static::class);
 }
 return $this->nodeName;
 }
}
