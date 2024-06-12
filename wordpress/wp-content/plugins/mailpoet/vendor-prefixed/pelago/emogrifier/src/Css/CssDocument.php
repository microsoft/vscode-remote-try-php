<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\Css;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Sabberworm\CSS\CSSList\AtRuleBlockList as CssAtRuleBlockList;
use MailPoetVendor\Sabberworm\CSS\CSSList\Document as SabberwormCssDocument;
use MailPoetVendor\Sabberworm\CSS\Parser as CssParser;
use MailPoetVendor\Sabberworm\CSS\Property\AtRule as CssAtRule;
use MailPoetVendor\Sabberworm\CSS\Property\Charset as CssCharset;
use MailPoetVendor\Sabberworm\CSS\Property\Import as CssImport;
use MailPoetVendor\Sabberworm\CSS\Renderable as CssRenderable;
use MailPoetVendor\Sabberworm\CSS\RuleSet\DeclarationBlock as CssDeclarationBlock;
use MailPoetVendor\Sabberworm\CSS\RuleSet\RuleSet as CssRuleSet;
class CssDocument
{
 private $sabberwormCssDocument;
 private $isImportRuleAllowed = \true;
 public function __construct(string $css)
 {
 $cssParser = new CssParser($css);
 $sabberwormCssDocument = $cssParser->parse();
 $this->sabberwormCssDocument = $sabberwormCssDocument;
 }
 public function getStyleRulesData(array $allowedMediaTypes) : array
 {
 $ruleMatches = [];
 foreach ($this->sabberwormCssDocument->getContents() as $rule) {
 if ($rule instanceof CssAtRuleBlockList) {
 $containingAtRule = $this->getFilteredAtIdentifierAndRule($rule, $allowedMediaTypes);
 if (\is_string($containingAtRule)) {
 foreach ($rule->getContents() as $nestedRule) {
 if ($nestedRule instanceof CssDeclarationBlock) {
 $ruleMatches[] = new StyleRule($nestedRule, $containingAtRule);
 }
 }
 }
 } elseif ($rule instanceof CssDeclarationBlock) {
 $ruleMatches[] = new StyleRule($rule);
 }
 }
 return $ruleMatches;
 }
 public function renderNonConditionalAtRules() : string
 {
 $this->isImportRuleAllowed = \true;
 $cssContents = $this->sabberwormCssDocument->getContents();
 $atRules = \array_filter($cssContents, [$this, 'isValidAtRuleToRender']);
 if ($atRules === []) {
 return '';
 }
 $atRulesDocument = new SabberwormCssDocument();
 $atRulesDocument->setContents($atRules);
 $renderedRules = $atRulesDocument->render();
 return $renderedRules;
 }
 private function getFilteredAtIdentifierAndRule(CssAtRuleBlockList $rule, array $allowedMediaTypes) : ?string
 {
 $result = null;
 if ($rule->atRuleName() === 'media') {
 $mediaQueryList = $rule->atRuleArgs();
 [$mediaType] = \explode('(', $mediaQueryList, 2);
 if (\trim($mediaType) !== '') {
 $escapedAllowedMediaTypes = \array_map(static function (string $allowedMediaType) : string {
 return \preg_quote($allowedMediaType, '/');
 }, $allowedMediaTypes);
 $mediaTypesMatcher = \implode('|', $escapedAllowedMediaTypes);
 $isAllowed = \preg_match('/^\\s*+(?:only\\s++)?+(?:' . $mediaTypesMatcher . ')/i', $mediaType) > 0;
 } else {
 $isAllowed = \true;
 }
 if ($isAllowed) {
 $result = '@media ' . $mediaQueryList;
 }
 }
 return $result;
 }
 private function isValidAtRuleToRender(CssRenderable $rule) : bool
 {
 if ($rule instanceof CssCharset) {
 return \false;
 }
 if ($rule instanceof CssImport) {
 return $this->isImportRuleAllowed;
 }
 $this->isImportRuleAllowed = \false;
 if (!$rule instanceof CssAtRule) {
 return \false;
 }
 switch ($rule->atRuleName()) {
 case 'media':
 $result = \false;
 break;
 case 'font-face':
 $result = $rule instanceof CssRuleSet && $rule->getRules('font-family') !== [] && $rule->getRules('src') !== [];
 break;
 default:
 $result = \true;
 }
 return $result;
 }
}
