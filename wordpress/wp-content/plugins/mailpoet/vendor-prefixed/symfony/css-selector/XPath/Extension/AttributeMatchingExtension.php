<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\XPath\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\XPath\Translator;
use MailPoetVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class AttributeMatchingExtension extends AbstractExtension
{
 public function getAttributeMatchingTranslators() : array
 {
 return ['exists' => [$this, 'translateExists'], '=' => [$this, 'translateEquals'], '~=' => [$this, 'translateIncludes'], '|=' => [$this, 'translateDashMatch'], '^=' => [$this, 'translatePrefixMatch'], '$=' => [$this, 'translateSuffixMatch'], '*=' => [$this, 'translateSubstringMatch'], '!=' => [$this, 'translateDifferent']];
 }
 public function translateExists(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition($attribute);
 }
 public function translateEquals(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition(\sprintf('%s = %s', $attribute, Translator::getXpathLiteral($value)));
 }
 public function translateIncludes(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition($value ? \sprintf('%1$s and contains(concat(\' \', normalize-space(%1$s), \' \'), %2$s)', $attribute, Translator::getXpathLiteral(' ' . $value . ' ')) : '0');
 }
 public function translateDashMatch(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition(\sprintf('%1$s and (%1$s = %2$s or starts-with(%1$s, %3$s))', $attribute, Translator::getXpathLiteral($value), Translator::getXpathLiteral($value . '-')));
 }
 public function translatePrefixMatch(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition($value ? \sprintf('%1$s and starts-with(%1$s, %2$s)', $attribute, Translator::getXpathLiteral($value)) : '0');
 }
 public function translateSuffixMatch(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition($value ? \sprintf('%1$s and substring(%1$s, string-length(%1$s)-%2$s) = %3$s', $attribute, \strlen($value) - 1, Translator::getXpathLiteral($value)) : '0');
 }
 public function translateSubstringMatch(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition($value ? \sprintf('%1$s and contains(%1$s, %2$s)', $attribute, Translator::getXpathLiteral($value)) : '0');
 }
 public function translateDifferent(XPathExpr $xpath, string $attribute, ?string $value) : XPathExpr
 {
 return $xpath->addCondition(\sprintf($value ? 'not(%1$s) or %1$s != %2$s' : '%s != %s', $attribute, Translator::getXpathLiteral($value)));
 }
 public function getName() : string
 {
 return 'attribute-matching';
 }
}
