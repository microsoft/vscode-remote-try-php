<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
class Markup implements \Countable, \JsonSerializable
{
 private $content;
 private $charset;
 public function __construct($content, $charset)
 {
 $this->content = (string) $content;
 $this->charset = $charset;
 }
 public function __toString()
 {
 return $this->content;
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 return \mb_strlen($this->content, $this->charset);
 }
 #[\ReturnTypeWillChange]
 public function jsonSerialize()
 {
 return $this->content;
 }
}
