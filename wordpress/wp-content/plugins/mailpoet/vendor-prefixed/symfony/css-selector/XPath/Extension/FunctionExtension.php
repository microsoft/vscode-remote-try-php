<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\XPath\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use MailPoetVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use MailPoetVendor\Symfony\Component\CssSelector\Node\FunctionNode;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Parser;
use MailPoetVendor\Symfony\Component\CssSelector\XPath\Translator;
use MailPoetVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class FunctionExtension extends AbstractExtension
{
 public function getFunctionTranslators() : array
 {
 return ['nth-child' => [$this, 'translateNthChild'], 'nth-last-child' => [$this, 'translateNthLastChild'], 'nth-of-type' => [$this, 'translateNthOfType'], 'nth-last-of-type' => [$this, 'translateNthLastOfType'], 'contains' => [$this, 'translateContains'], 'lang' => [$this, 'translateLang']];
 }
 public function translateNthChild(XPathExpr $xpath, FunctionNode $function, bool $last = \false, bool $addNameTest = \true) : XPathExpr
 {
 try {
 [$a, $b] = Parser::parseSeries($function->getArguments());
 } catch (SyntaxErrorException $e) {
 throw new ExpressionErrorException(\sprintf('Invalid series: "%s".', \implode('", "', $function->getArguments())), 0, $e);
 }
 $xpath->addStarPrefix();
 if ($addNameTest) {
 $xpath->addNameTest();
 }
 if (0 === $a) {
 return $xpath->addCondition('position() = ' . ($last ? 'last() - ' . ($b - 1) : $b));
 }
 if ($a < 0) {
 if ($b < 1) {
 return $xpath->addCondition('false()');
 }
 $sign = '<=';
 } else {
 $sign = '>=';
 }
 $expr = 'position()';
 if ($last) {
 $expr = 'last() - ' . $expr;
 --$b;
 }
 if (0 !== $b) {
 $expr .= ' - ' . $b;
 }
 $conditions = [\sprintf('%s %s 0', $expr, $sign)];
 if (1 !== $a && -1 !== $a) {
 $conditions[] = \sprintf('(%s) mod %d = 0', $expr, $a);
 }
 return $xpath->addCondition(\implode(' and ', $conditions));
 // todo: handle an+b, odd, even
 // an+b means every-a, plus b, e.g., 2n+1 means odd
 // 0n+b means b
 // n+0 means a=1, i.e., all elements
 // an means every a elements, i.e., 2n means even
 // -n means -1n
 // -1n+6 means elements 6 and previous
 }
 public function translateNthLastChild(XPathExpr $xpath, FunctionNode $function) : XPathExpr
 {
 return $this->translateNthChild($xpath, $function, \true);
 }
 public function translateNthOfType(XPathExpr $xpath, FunctionNode $function) : XPathExpr
 {
 return $this->translateNthChild($xpath, $function, \false, \false);
 }
 public function translateNthLastOfType(XPathExpr $xpath, FunctionNode $function) : XPathExpr
 {
 if ('*' === $xpath->getElement()) {
 throw new ExpressionErrorException('"*:nth-of-type()" is not implemented.');
 }
 return $this->translateNthChild($xpath, $function, \true, \false);
 }
 public function translateContains(XPathExpr $xpath, FunctionNode $function) : XPathExpr
 {
 $arguments = $function->getArguments();
 foreach ($arguments as $token) {
 if (!($token->isString() || $token->isIdentifier())) {
 throw new ExpressionErrorException('Expected a single string or identifier for :contains(), got ' . \implode(', ', $arguments));
 }
 }
 return $xpath->addCondition(\sprintf('contains(string(.), %s)', Translator::getXpathLiteral($arguments[0]->getValue())));
 }
 public function translateLang(XPathExpr $xpath, FunctionNode $function) : XPathExpr
 {
 $arguments = $function->getArguments();
 foreach ($arguments as $token) {
 if (!($token->isString() || $token->isIdentifier())) {
 throw new ExpressionErrorException('Expected a single string or identifier for :lang(), got ' . \implode(', ', $arguments));
 }
 }
 return $xpath->addCondition(\sprintf('lang(%s)', Translator::getXpathLiteral($arguments[0]->getValue())));
 }
 public function getName() : string
 {
 return 'function';
 }
}
