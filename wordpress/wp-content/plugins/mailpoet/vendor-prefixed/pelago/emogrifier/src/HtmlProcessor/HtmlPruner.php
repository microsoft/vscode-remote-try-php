<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\HtmlProcessor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Pelago\Emogrifier\CssInliner;
use MailPoetVendor\Pelago\Emogrifier\Utilities\ArrayIntersector;
class HtmlPruner extends AbstractHtmlProcessor
{
 private const DISPLAY_NONE_MATCHER = '//*[@style and contains(translate(translate(@style," ",""),"NOE","noe"),"display:none")' . ' and not(@class and contains(concat(" ", normalize-space(@class), " "), " -emogrifier-keep "))]';
 public function removeElementsWithDisplayNone() : self
 {
 $elementsWithStyleDisplayNone = $this->getXPath()->query(self::DISPLAY_NONE_MATCHER);
 if ($elementsWithStyleDisplayNone->length === 0) {
 return $this;
 }
 foreach ($elementsWithStyleDisplayNone as $element) {
 $parentNode = $element->parentNode;
 if ($parentNode !== null) {
 $parentNode->removeChild($element);
 }
 }
 return $this;
 }
 public function removeRedundantClasses(array $classesToKeep = []) : self
 {
 $elementsWithClassAttribute = $this->getXPath()->query('//*[@class]');
 if ($classesToKeep !== []) {
 $this->removeClassesFromElements($elementsWithClassAttribute, $classesToKeep);
 } else {
 // Avoid unnecessary processing if there are no classes to keep.
 $this->removeClassAttributeFromElements($elementsWithClassAttribute);
 }
 return $this;
 }
 private function removeClassesFromElements(\DOMNodeList $elements, array $classesToKeep) : void
 {
 $classesToKeepIntersector = new ArrayIntersector($classesToKeep);
 foreach ($elements as $element) {
 $elementClasses = \preg_split('/\\s++/', \trim($element->getAttribute('class')));
 $elementClassesToKeep = $classesToKeepIntersector->intersectWith($elementClasses);
 if ($elementClassesToKeep !== []) {
 $element->setAttribute('class', \implode(' ', $elementClassesToKeep));
 } else {
 $element->removeAttribute('class');
 }
 }
 }
 private function removeClassAttributeFromElements(\DOMNodeList $elements) : void
 {
 foreach ($elements as $element) {
 $element->removeAttribute('class');
 }
 }
 public function removeRedundantClassesAfterCssInlined(CssInliner $cssInliner) : self
 {
 $classesToKeepAsKeys = [];
 foreach ($cssInliner->getMatchingUninlinableSelectors() as $selector) {
 \preg_match_all('/\\.(-?+[_a-zA-Z][\\w\\-]*+)/', $selector, $matches);
 $classesToKeepAsKeys += \array_fill_keys($matches[1], \true);
 }
 $this->removeRedundantClasses(\array_keys($classesToKeepAsKeys));
 return $this;
 }
}
