<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\XPath\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class CombinationExtension extends AbstractExtension
{
 public function getCombinationTranslators() : array
 {
 return [' ' => [$this, 'translateDescendant'], '>' => [$this, 'translateChild'], '+' => [$this, 'translateDirectAdjacent'], '~' => [$this, 'translateIndirectAdjacent']];
 }
 public function translateDescendant(XPathExpr $xpath, XPathExpr $combinedXpath) : XPathExpr
 {
 return $xpath->join('/descendant-or-self::*/', $combinedXpath);
 }
 public function translateChild(XPathExpr $xpath, XPathExpr $combinedXpath) : XPathExpr
 {
 return $xpath->join('/', $combinedXpath);
 }
 public function translateDirectAdjacent(XPathExpr $xpath, XPathExpr $combinedXpath) : XPathExpr
 {
 return $xpath->join('/following-sibling::', $combinedXpath)->addNameTest()->addCondition('position() = 1');
 }
 public function translateIndirectAdjacent(XPathExpr $xpath, XPathExpr $combinedXpath) : XPathExpr
 {
 return $xpath->join('/following-sibling::', $combinedXpath);
 }
 public function getName() : string
 {
 return 'combination';
 }
}
