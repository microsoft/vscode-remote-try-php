<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\XPath\Extension;
if (!defined('ABSPATH')) exit;
abstract class AbstractExtension implements ExtensionInterface
{
 public function getNodeTranslators() : array
 {
 return [];
 }
 public function getCombinationTranslators() : array
 {
 return [];
 }
 public function getFunctionTranslators() : array
 {
 return [];
 }
 public function getPseudoClassTranslators() : array
 {
 return [];
 }
 public function getAttributeMatchingTranslators() : array
 {
 return [];
 }
}
