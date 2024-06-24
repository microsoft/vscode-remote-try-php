<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\HtmlProcessor;
if (!defined('ABSPATH')) exit;
class CssToAttributeConverter extends AbstractHtmlProcessor
{
 private $cssToHtmlMap = ['background-color' => ['attribute' => 'bgcolor'], 'text-align' => ['attribute' => 'align', 'nodes' => ['p', 'div', 'td', 'th'], 'values' => ['left', 'right', 'center', 'justify']], 'float' => ['attribute' => 'align', 'nodes' => ['table', 'img'], 'values' => ['left', 'right']], 'border-spacing' => ['attribute' => 'cellspacing', 'nodes' => ['table']]];
 private static $parsedCssCache = [];
 public function convertCssToVisualAttributes() : self
 {
 foreach ($this->getAllNodesWithStyleAttribute() as $node) {
 $inlineStyleDeclarations = $this->parseCssDeclarationsBlock($node->getAttribute('style'));
 $this->mapCssToHtmlAttributes($inlineStyleDeclarations, $node);
 }
 return $this;
 }
 private function getAllNodesWithStyleAttribute() : \DOMNodeList
 {
 return $this->getXPath()->query('//*[@style]');
 }
 private function parseCssDeclarationsBlock(string $cssDeclarationsBlock) : array
 {
 if (isset(self::$parsedCssCache[$cssDeclarationsBlock])) {
 return self::$parsedCssCache[$cssDeclarationsBlock];
 }
 $properties = [];
 foreach (\preg_split('/;(?!base64|charset)/', $cssDeclarationsBlock) as $declaration) {
 $matches = [];
 if (!\preg_match('/^([A-Za-z\\-]+)\\s*:\\s*(.+)$/s', \trim($declaration), $matches)) {
 continue;
 }
 $propertyName = \strtolower($matches[1]);
 $propertyValue = $matches[2];
 $properties[$propertyName] = $propertyValue;
 }
 self::$parsedCssCache[$cssDeclarationsBlock] = $properties;
 return $properties;
 }
 private function mapCssToHtmlAttributes(array $styles, \DOMElement $node) : void
 {
 foreach ($styles as $property => $value) {
 // Strip !important indicator
 $value = \trim(\str_replace('!important', '', $value));
 $this->mapCssToHtmlAttribute($property, $value, $node);
 }
 }
 private function mapCssToHtmlAttribute(string $property, string $value, \DOMElement $node) : void
 {
 if (!$this->mapSimpleCssProperty($property, $value, $node)) {
 $this->mapComplexCssProperty($property, $value, $node);
 }
 }
 private function mapSimpleCssProperty(string $property, string $value, \DOMElement $node) : bool
 {
 if (!isset($this->cssToHtmlMap[$property])) {
 return \false;
 }
 $mapping = $this->cssToHtmlMap[$property];
 $nodesMatch = !isset($mapping['nodes']) || \in_array($node->nodeName, $mapping['nodes'], \true);
 $valuesMatch = !isset($mapping['values']) || \in_array($value, $mapping['values'], \true);
 $canBeMapped = $nodesMatch && $valuesMatch;
 if ($canBeMapped) {
 $node->setAttribute($mapping['attribute'], $value);
 }
 return $canBeMapped;
 }
 private function mapComplexCssProperty(string $property, string $value, \DOMElement $node) : void
 {
 switch ($property) {
 case 'background':
 $this->mapBackgroundProperty($node, $value);
 break;
 case 'width':
 // intentional fall-through
 case 'height':
 $this->mapWidthOrHeightProperty($node, $value, $property);
 break;
 case 'margin':
 $this->mapMarginProperty($node, $value);
 break;
 case 'border':
 $this->mapBorderProperty($node, $value);
 break;
 default:
 }
 }
 private function mapBackgroundProperty(\DOMElement $node, string $value) : void
 {
 // parse out the color, if any
 $styles = \explode(' ', $value, 2);
 $first = $styles[0];
 if (\is_numeric($first[0]) || \strncmp($first, 'url', 3) === 0) {
 return;
 }
 // as this is not a position or image, assume it's a color
 $node->setAttribute('bgcolor', $first);
 }
 private function mapWidthOrHeightProperty(\DOMElement $node, string $value, string $property) : void
 {
 // only parse values in px and %, but not values like "auto"
 if (!\preg_match('/^(\\d+)(\\.(\\d+))?(px|%)$/', $value)) {
 return;
 }
 $number = \preg_replace('/[^0-9.%]/', '', $value);
 $node->setAttribute($property, $number);
 }
 private function mapMarginProperty(\DOMElement $node, string $value) : void
 {
 if (!$this->isTableOrImageNode($node)) {
 return;
 }
 $margins = $this->parseCssShorthandValue($value);
 if ($margins['left'] === 'auto' && $margins['right'] === 'auto') {
 $node->setAttribute('align', 'center');
 }
 }
 private function mapBorderProperty(\DOMElement $node, string $value) : void
 {
 if (!$this->isTableOrImageNode($node)) {
 return;
 }
 if ($value === 'none' || $value === '0') {
 $node->setAttribute('border', '0');
 }
 }
 private function isTableOrImageNode(\DOMElement $node) : bool
 {
 return $node->nodeName === 'table' || $node->nodeName === 'img';
 }
 private function parseCssShorthandValue(string $value) : array
 {
 $values = \preg_split('/\\s+/', $value);
 $css = [];
 $css['top'] = $values[0];
 $css['right'] = \count($values) > 1 ? $values[1] : $css['top'];
 $css['bottom'] = \count($values) > 2 ? $values[2] : $css['top'];
 $css['left'] = \count($values) > 3 ? $values[3] : $css['right'];
 return $css;
 }
}
