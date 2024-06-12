<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\XPath\Extension;
if (!defined('ABSPATH')) exit;
interface ExtensionInterface
{
 public function getNodeTranslators() : array;
 public function getCombinationTranslators() : array;
 public function getFunctionTranslators() : array;
 public function getPseudoClassTranslators() : array;
 public function getAttributeMatchingTranslators() : array;
 public function getName() : string;
}
