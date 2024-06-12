<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\Utilities;
if (!defined('ABSPATH')) exit;
class CssConcatenator
{
 private $mediaRules = [];
 public function append(array $selectors, string $declarationsBlock, string $media = '') : void
 {
 $selectorsAsKeys = \array_flip($selectors);
 $mediaRule = $this->getOrCreateMediaRuleToAppendTo($media);
 $ruleBlocks = $mediaRule->ruleBlocks;
 $lastRuleBlock = \end($ruleBlocks);
 $hasSameDeclarationsAsLastRule = \is_object($lastRuleBlock) && $declarationsBlock === $lastRuleBlock->declarationsBlock;
 if ($hasSameDeclarationsAsLastRule) {
 $lastRuleBlock->selectorsAsKeys += $selectorsAsKeys;
 } else {
 $lastRuleBlockSelectors = \is_object($lastRuleBlock) ? $lastRuleBlock->selectorsAsKeys : [];
 $hasSameSelectorsAsLastRule = \is_object($lastRuleBlock) && self::hasEquivalentSelectors($selectorsAsKeys, $lastRuleBlockSelectors);
 if ($hasSameSelectorsAsLastRule) {
 $lastDeclarationsBlockWithoutSemicolon = \rtrim(\rtrim($lastRuleBlock->declarationsBlock), ';');
 $lastRuleBlock->declarationsBlock = $lastDeclarationsBlockWithoutSemicolon . ';' . $declarationsBlock;
 } else {
 $mediaRule->ruleBlocks[] = (object) \compact('selectorsAsKeys', 'declarationsBlock');
 }
 }
 }
 public function getCss() : string
 {
 return \implode('', \array_map([self::class, 'getMediaRuleCss'], $this->mediaRules));
 }
 private function getOrCreateMediaRuleToAppendTo(string $media) : object
 {
 $lastMediaRule = \end($this->mediaRules);
 if (\is_object($lastMediaRule) && $media === $lastMediaRule->media) {
 return $lastMediaRule;
 }
 $newMediaRule = (object) ['media' => $media, 'ruleBlocks' => []];
 $this->mediaRules[] = $newMediaRule;
 return $newMediaRule;
 }
 private static function hasEquivalentSelectors(array $selectorsAsKeys1, array $selectorsAsKeys2) : bool
 {
 return \count($selectorsAsKeys1) === \count($selectorsAsKeys2) && \count($selectorsAsKeys1) === \count($selectorsAsKeys1 + $selectorsAsKeys2);
 }
 private static function getMediaRuleCss(object $mediaRule) : string
 {
 $ruleBlocks = $mediaRule->ruleBlocks;
 $css = \implode('', \array_map([self::class, 'getRuleBlockCss'], $ruleBlocks));
 $media = $mediaRule->media;
 if ($media !== '') {
 $css = $media . '{' . $css . '}';
 }
 return $css;
 }
 private static function getRuleBlockCss(object $ruleBlock) : string
 {
 $selectorsAsKeys = $ruleBlock->selectorsAsKeys;
 $selectors = \array_keys($selectorsAsKeys);
 $declarationsBlock = $ruleBlock->declarationsBlock;
 return \implode(',', $selectors) . '{' . $declarationsBlock . '}';
 }
}
