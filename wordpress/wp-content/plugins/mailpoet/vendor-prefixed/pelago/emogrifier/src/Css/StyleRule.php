<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\Css;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\Property\Selector;
use MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock;
class StyleRule
{
 private $declarationBlock;
 private $containingAtRule;
 public function __construct(DeclarationBlock $declarationBlock, string $containingAtRule = '')
 {
 $this->declarationBlock = $declarationBlock;
 $this->containingAtRule = \trim($containingAtRule);
 }
 public function getSelectors() : array
 {
 $selectors = $this->declarationBlock->getSelectors();
 return \array_map(static function (Selector $selector) : string {
 return (string) $selector;
 }, $selectors);
 }
 public function getDeclarationAsText() : string
 {
 return \implode(' ', $this->declarationBlock->getRules());
 }
 public function hasAtLeastOneDeclaration() : bool
 {
 return $this->declarationBlock->getRules() !== [];
 }
 public function getContainingAtRule() : string
 {
 return $this->containingAtRule;
 }
 public function hasContainingAtRule() : bool
 {
 return $this->getContainingAtRule() !== '';
 }
}
