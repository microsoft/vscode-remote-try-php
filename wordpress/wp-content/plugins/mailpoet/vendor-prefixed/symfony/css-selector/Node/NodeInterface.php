<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Node;
if (!defined('ABSPATH')) exit;
interface NodeInterface
{
 public function getNodeName() : string;
 public function getSpecificity() : Specificity;
 public function __toString() : string;
}
