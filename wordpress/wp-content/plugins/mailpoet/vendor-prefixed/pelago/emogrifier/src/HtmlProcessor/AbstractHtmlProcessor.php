<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\HtmlProcessor;
if (!defined('ABSPATH')) exit;
abstract class AbstractHtmlProcessor
{
 protected const DEFAULT_DOCUMENT_TYPE = '<!DOCTYPE html>';
 protected const CONTENT_TYPE_META_TAG = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
 protected const PHP_UNRECOGNIZED_VOID_TAGNAME_MATCHER = '(?:command|embed|keygen|source|track|wbr)';
 protected const TAGNAME_ALLOWED_BEFORE_BODY_MATCHER = '(?:html|head|base|command|link|meta|noscript|script|style|template|title)';
 protected const HTML_COMMENT_PATTERN = '/<!--[^-]*+(?:-(?!->)[^-]*+)*+(?:-->|$)/';
 protected const HTML_TEMPLATE_ELEMENT_PATTERN = '%<template[\\s>][^<]*+(?:<(?!/template>)[^<]*+)*+(?:</template>|$)%i';
 protected $domDocument = null;
 private $xPath = null;
 private function __construct()
 {
 }
 public static function fromHtml(string $unprocessedHtml) : self
 {
 if ($unprocessedHtml === '') {
 throw new \InvalidArgumentException('The provided HTML must not be empty.', 1515763647);
 }
 $instance = new static();
 $instance->setHtml($unprocessedHtml);
 return $instance;
 }
 public static function fromDomDocument(\DOMDocument $document) : self
 {
 $instance = new static();
 $instance->setDomDocument($document);
 return $instance;
 }
 private function setHtml(string $html) : void
 {
 $this->createUnifiedDomDocument($html);
 }
 public function getDomDocument() : \DOMDocument
 {
 if (!$this->domDocument instanceof \DOMDocument) {
 $message = self::class . '::setDomDocument() has not yet been called on ' . static::class;
 throw new \UnexpectedValueException($message, 1570472239);
 }
 return $this->domDocument;
 }
 private function setDomDocument(\DOMDocument $domDocument) : void
 {
 $this->domDocument = $domDocument;
 $this->xPath = new \DOMXPath($this->domDocument);
 }
 protected function getXPath() : \DOMXPath
 {
 if (!$this->xPath instanceof \DOMXPath) {
 $message = self::class . '::setDomDocument() has not yet been called on ' . static::class;
 throw new \UnexpectedValueException($message, 1617819086);
 }
 return $this->xPath;
 }
 public function render() : string
 {
 $htmlWithPossibleErroneousClosingTags = $this->getDomDocument()->saveHTML();
 return $this->removeSelfClosingTagsClosingTags($htmlWithPossibleErroneousClosingTags);
 }
 public function renderBodyContent() : string
 {
 $htmlWithPossibleErroneousClosingTags = $this->getDomDocument()->saveHTML($this->getBodyElement());
 $bodyNodeHtml = $this->removeSelfClosingTagsClosingTags($htmlWithPossibleErroneousClosingTags);
 return \preg_replace('%</?+body(?:\\s[^>]*+)?+>%', '', $bodyNodeHtml);
 }
 private function removeSelfClosingTagsClosingTags(string $html) : string
 {
 return \preg_replace('%</' . self::PHP_UNRECOGNIZED_VOID_TAGNAME_MATCHER . '>%', '', $html);
 }
 private function getBodyElement() : \DOMElement
 {
 $node = $this->getDomDocument()->getElementsByTagName('body')->item(0);
 if (!$node instanceof \DOMElement) {
 throw new \RuntimeException('There is no body element.', 1617922607);
 }
 return $node;
 }
 private function createUnifiedDomDocument(string $html) : void
 {
 $this->createRawDomDocument($html);
 $this->ensureExistenceOfBodyElement();
 }
 private function createRawDomDocument(string $html) : void
 {
 $domDocument = new \DOMDocument();
 $domDocument->strictErrorChecking = \false;
 $domDocument->formatOutput = \true;
 $libXmlState = \libxml_use_internal_errors(\true);
 $domDocument->loadHTML($this->prepareHtmlForDomConversion($html));
 \libxml_clear_errors();
 \libxml_use_internal_errors($libXmlState);
 $this->setDomDocument($domDocument);
 }
 private function prepareHtmlForDomConversion(string $html) : string
 {
 $htmlWithSelfClosingSlashes = $this->ensurePhpUnrecognizedSelfClosingTagsAreXml($html);
 $htmlWithDocumentType = $this->ensureDocumentType($htmlWithSelfClosingSlashes);
 return $this->addContentTypeMetaTag($htmlWithDocumentType);
 }
 private function ensureDocumentType(string $html) : string
 {
 $hasDocumentType = \stripos($html, '<!DOCTYPE') !== \false;
 if ($hasDocumentType) {
 return $this->normalizeDocumentType($html);
 }
 return self::DEFAULT_DOCUMENT_TYPE . $html;
 }
 private function normalizeDocumentType(string $html) : string
 {
 // Limit to replacing the first occurrence: as an optimization; and in case an example exists as unescaped text.
 return \preg_replace('/<!DOCTYPE\\s++html(?=[\\s>])/i', '<!DOCTYPE html', $html, 1);
 }
 private function addContentTypeMetaTag(string $html) : string
 {
 if ($this->hasContentTypeMetaTagInHead($html)) {
 return $html;
 }
 // We are trying to insert the meta tag to the right spot in the DOM.
 // If we just prepended it to the HTML, we would lose attributes set to the HTML tag.
 $hasHeadTag = \preg_match('/<head[\\s>]/i', $html);
 $hasHtmlTag = \stripos($html, '<html') !== \false;
 if ($hasHeadTag) {
 $reworkedHtml = \preg_replace('/<head(?=[\\s>])([^>]*+)>/i', '<head$1>' . self::CONTENT_TYPE_META_TAG, $html);
 } elseif ($hasHtmlTag) {
 $reworkedHtml = \preg_replace('/<html(.*?)>/is', '<html$1><head>' . self::CONTENT_TYPE_META_TAG . '</head>', $html);
 } else {
 $reworkedHtml = self::CONTENT_TYPE_META_TAG . $html;
 }
 return $reworkedHtml;
 }
 private function hasContentTypeMetaTagInHead(string $html) : bool
 {
 \preg_match('%^.*?(?=<meta(?=\\s)[^>]*\\shttp-equiv=(["\']?+)Content-Type\\g{-1}[\\s/>])%is', $html, $matches);
 if (isset($matches[0])) {
 $htmlBefore = $matches[0];
 try {
 $hasContentTypeMetaTagInHead = !$this->hasEndOfHeadElement($htmlBefore);
 } catch (\RuntimeException $exception) {
 // If something unexpected occurs, assume the `Content-Type` that was found is valid.
 \trigger_error($exception->getMessage());
 $hasContentTypeMetaTagInHead = \true;
 }
 } else {
 $hasContentTypeMetaTagInHead = \false;
 }
 return $hasContentTypeMetaTagInHead;
 }
 private function hasEndOfHeadElement(string $html) : bool
 {
 $headEndTagMatchCount = \preg_match('%<(?!' . self::TAGNAME_ALLOWED_BEFORE_BODY_MATCHER . '[\\s/>])\\w|</head>%i', $html);
 if (\is_int($headEndTagMatchCount) && $headEndTagMatchCount > 0) {
 // An exception to the implicit end of the `<head>` is any content within a `<template>` element, as well in
 // comments. As an optimization, this is only checked for if a potential `<head>` end tag is found.
 $htmlWithoutCommentsOrTemplates = $this->removeHtmlTemplateElements($this->removeHtmlComments($html));
 $hasEndOfHeadElement = $htmlWithoutCommentsOrTemplates === $html || $this->hasEndOfHeadElement($htmlWithoutCommentsOrTemplates);
 } else {
 $hasEndOfHeadElement = \false;
 }
 return $hasEndOfHeadElement;
 }
 private function removeHtmlComments(string $html) : string
 {
 $result = \preg_replace(self::HTML_COMMENT_PATTERN, '', $html);
 if (!\is_string($result)) {
 throw new \RuntimeException('Internal PCRE error', 1616521475);
 }
 return $result;
 }
 private function removeHtmlTemplateElements(string $html) : string
 {
 $result = \preg_replace(self::HTML_TEMPLATE_ELEMENT_PATTERN, '', $html);
 if (!\is_string($result)) {
 throw new \RuntimeException('Internal PCRE error', 1616519652);
 }
 return $result;
 }
 private function ensurePhpUnrecognizedSelfClosingTagsAreXml(string $html) : string
 {
 return \preg_replace('%<' . self::PHP_UNRECOGNIZED_VOID_TAGNAME_MATCHER . '\\b[^>]*+(?<!/)(?=>)%', '$0/', $html);
 }
 private function ensureExistenceOfBodyElement() : void
 {
 if ($this->getDomDocument()->getElementsByTagName('body')->item(0) instanceof \DOMElement) {
 return;
 }
 $htmlElement = $this->getDomDocument()->getElementsByTagName('html')->item(0);
 if (!$htmlElement instanceof \DOMElement) {
 throw new \UnexpectedValueException('There is no HTML element although there should be one.', 1569930853);
 }
 $htmlElement->appendChild($this->getDomDocument()->createElement('body'));
 }
}
