<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\XPath\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use MailPoetVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class PseudoClassExtension extends AbstractExtension
{
 public function getPseudoClassTranslators() : array
 {
 return ['root' => [$this, 'translateRoot'], 'first-child' => [$this, 'translateFirstChild'], 'last-child' => [$this, 'translateLastChild'], 'first-of-type' => [$this, 'translateFirstOfType'], 'last-of-type' => [$this, 'translateLastOfType'], 'only-child' => [$this, 'translateOnlyChild'], 'only-of-type' => [$this, 'translateOnlyOfType'], 'empty' => [$this, 'translateEmpty']];
 }
 public function translateRoot(XPathExpr $xpath) : XPathExpr
 {
 return $xpath->addCondition('not(parent::*)');
 }
 public function translateFirstChild(XPathExpr $xpath) : XPathExpr
 {
 return $xpath->addStarPrefix()->addNameTest()->addCondition('position() = 1');
 }
 public function translateLastChild(XPathExpr $xpath) : XPathExpr
 {
 return $xpath->addStarPrefix()->addNameTest()->addCondition('position() = last()');
 }
 public function translateFirstOfType(XPathExpr $xpath) : XPathExpr
 {
 if ('*' === $xpath->getElement()) {
 throw new ExpressionErrorException('"*:first-of-type" is not implemented.');
 }
 return $xpath->addStarPrefix()->addCondition('position() = 1');
 }
 public function translateLastOfType(XPathExpr $xpath) : XPathExpr
 {
 if ('*' === $xpath->getElement()) {
 throw new ExpressionErrorException('"*:last-of-type" is not implemented.');
 }
 return $xpath->addStarPrefix()->addCondition('position() = last()');
 }
 public function translateOnlyChild(XPathExpr $xpath) : XPathExpr
 {
 return $xpath->addStarPrefix()->addNameTest()->addCondition('last() = 1');
 }
 public function translateOnlyOfType(XPathExpr $xpath) : XPathExpr
 {
 $element = $xpath->getElement();
 return $xpath->addCondition(\sprintf('count(preceding-sibling::%s)=0 and count(following-sibling::%s)=0', $element, $element));
 }
 public function translateEmpty(XPathExpr $xpath) : XPathExpr
 {
 return $xpath->addCondition('not(*) and not(string-length())');
 }
 public function getName() : string
 {
 return 'pseudo-class';
 }
}
