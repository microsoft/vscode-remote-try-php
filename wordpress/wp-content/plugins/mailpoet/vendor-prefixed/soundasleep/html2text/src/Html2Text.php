<?php
namespace MailPoetVendor\Html2Text;
if (!defined('ABSPATH')) exit;
class Html2Text
{
 public static function convert($html, $ignore_error = \false)
 {
 $is_office_document = static::isOfficeDocument($html);
 if ($is_office_document) {
 // remove office namespace
 $html = \str_replace(array("<o:p>", "</o:p>"), "", $html);
 }
 $html = static::fixNewlines($html);
 if (\mb_detect_encoding($html, "UTF-8", \true)) {
 $html = \mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8");
 }
 $doc = static::getDocument($html, $ignore_error);
 $output = static::iterateOverNode($doc, null, \false, $is_office_document);
 // process output for whitespace/newlines
 $output = static::processWhitespaceNewlines($output);
 return $output;
 }
 static function fixNewlines($text)
 {
 // replace \r\n to \n
 $text = \str_replace("\r\n", "\n", $text);
 // remove \rs
 $text = \str_replace("\r", "\n", $text);
 return $text;
 }
 static function processWhitespaceNewlines($text)
 {
 // remove excess spaces around tabs
 $text = \preg_replace("/ *\t */im", "\t", $text);
 // remove leading whitespace
 $text = \ltrim($text);
 // remove leading spaces on each line
 $text = \preg_replace("/\n[ \t]*/im", "\n", $text);
 // convert non-breaking spaces to regular spaces to prevent output issues,
 // do it here so they do NOT get removed with other leading spaces, as they
 // are sometimes used for indentation
 $text = \str_replace(" ", " ", $text);
 // remove trailing whitespace
 $text = \rtrim($text);
 // remove trailing spaces on each line
 $text = \preg_replace("/[ \t]*\n/im", "\n", $text);
 // unarmor pre blocks
 $text = static::fixNewLines($text);
 // remove unnecessary empty lines
 $text = \preg_replace("/\n\n\n*/im", "\n\n", $text);
 return $text;
 }
 static function getDocument($html, $ignore_error = \false)
 {
 $doc = new \DOMDocument();
 $html = \trim($html);
 if (!$html) {
 // DOMDocument doesn't support empty value and throws an error
 // Return empty document instead
 return $doc;
 }
 if ($html[0] !== '<') {
 // If HTML does not begin with a tag, we put a body tag around it.
 // If we do not do this, PHP will insert a paragraph tag around
 // the first block of text for some reason which can mess up
 // the newlines. See pre.html test for an example.
 $html = '<body>' . $html . '</body>';
 }
 if ($ignore_error) {
 $doc->strictErrorChecking = \false;
 $doc->recover = \true;
 $doc->xmlStandalone = \true;
 $old_internal_errors = \libxml_use_internal_errors(\true);
 $load_result = $doc->loadHTML($html, \LIBXML_NOWARNING | \LIBXML_NOERROR | \LIBXML_NONET | \LIBXML_PARSEHUGE);
 \libxml_use_internal_errors($old_internal_errors);
 } else {
 $load_result = $doc->loadHTML($html);
 }
 if (!$load_result) {
 throw new Html2TextException("Could not load HTML - badly formed?", $html);
 }
 return $doc;
 }
 static function isOfficeDocument($html)
 {
 return \strpos($html, "urn:schemas-microsoft-com:office") !== \false;
 }
 static function isWhitespace($text)
 {
 return \strlen(\trim($text, "\n\r\t ")) === 0;
 }
 static function nextChildName($node)
 {
 // get the next child
 $nextNode = $node->nextSibling;
 while ($nextNode != null) {
 if ($nextNode instanceof \DOMText) {
 if (!static::isWhitespace($nextNode->wholeText)) {
 break;
 }
 }
 if ($nextNode instanceof \DOMElement) {
 break;
 }
 $nextNode = $nextNode->nextSibling;
 }
 $nextName = null;
 if (($nextNode instanceof \DOMElement || $nextNode instanceof \DOMText) && $nextNode != null) {
 $nextName = \strtolower($nextNode->nodeName);
 }
 return $nextName;
 }
 static function iterateOverNode($node, $prevName = null, $in_pre = \false, $is_office_document = \false)
 {
 if ($node instanceof \DOMText) {
 // Replace whitespace characters with a space (equivilant to \s)
 if ($in_pre) {
 $text = "\n" . \trim($node->wholeText, "\n\r\t ") . "\n";
 // Remove trailing whitespace only
 $text = \preg_replace("/[ \t]*\n/im", "\n", $text);
 // armor newlines with \r.
 return \str_replace("\n", "\r", $text);
 } else {
 $text = \preg_replace("/[\\t\\n\\f\\r ]+/im", " ", $node->wholeText);
 if (!static::isWhitespace($text) && ($prevName == 'p' || $prevName == 'div')) {
 return "\n" . $text;
 }
 return $text;
 }
 }
 if ($node instanceof \DOMDocumentType) {
 // ignore
 return "";
 }
 if ($node instanceof \DOMProcessingInstruction) {
 // ignore
 return "";
 }
 $name = \strtolower($node->nodeName);
 $nextName = static::nextChildName($node);
 // start whitespace
 switch ($name) {
 case "hr":
 $prefix = '';
 if ($prevName != null) {
 $prefix = "\n";
 }
 return $prefix . "---------------------------------------------------------------\n";
 case "style":
 case "head":
 case "title":
 case "meta":
 case "script":
 // ignore these tags
 return "";
 case "h1":
 case "h2":
 case "h3":
 case "h4":
 case "h5":
 case "h6":
 case "ol":
 case "ul":
 case "pre":
 // add two newlines
 $output = "\n\n";
 break;
 case "td":
 case "th":
 // add tab char to separate table fields
 $output = "\t";
 break;
 case "p":
 // Microsoft exchange emails often include HTML which, when passed through
 // html2text, results in lots of double line returns everywhere.
 //
 // To fix this, for any p element with a className of `MsoNormal` (the standard
 // classname in any Microsoft export or outlook for a paragraph that behaves
 // like a line return) we skip the first line returns and set the name to br.
 if ($is_office_document && $node->getAttribute('class') == 'MsoNormal') {
 $output = "";
 $name = 'br';
 break;
 }
 // add two lines
 $output = "\n\n";
 break;
 case "tr":
 // add one line
 $output = "\n";
 break;
 case "div":
 $output = "";
 if ($prevName !== null) {
 // add one line
 $output .= "\n";
 }
 break;
 case "li":
 $output = "- ";
 break;
 default:
 // print out contents of unknown tags
 $output = "";
 break;
 }
 // debug
 //$output .= "[$name,$nextName]";
 if (isset($node->childNodes)) {
 $n = $node->childNodes->item(0);
 $previousSiblingNames = array();
 $previousSiblingName = null;
 $parts = array();
 $trailing_whitespace = 0;
 while ($n != null) {
 $text = static::iterateOverNode($n, $previousSiblingName, $in_pre || $name == 'pre', $is_office_document);
 // Pass current node name to next child, as previousSibling does not appear to get populated
 if ($n instanceof \DOMDocumentType || $n instanceof \DOMProcessingInstruction || $n instanceof \DOMText && static::isWhitespace($text)) {
 // Keep current previousSiblingName, these are invisible
 $trailing_whitespace++;
 } else {
 $previousSiblingName = \strtolower($n->nodeName);
 $previousSiblingNames[] = $previousSiblingName;
 $trailing_whitespace = 0;
 }
 $node->removeChild($n);
 $n = $node->childNodes->item(0);
 $parts[] = $text;
 }
 // Remove trailing whitespace, important for the br check below
 while ($trailing_whitespace-- > 0) {
 \array_pop($parts);
 }
 // suppress last br tag inside a node list if follows text
 $last_name = \array_pop($previousSiblingNames);
 if ($last_name === 'br') {
 $last_name = \array_pop($previousSiblingNames);
 if ($last_name === '#text') {
 \array_pop($parts);
 }
 }
 $output .= \implode('', $parts);
 }
 // end whitespace
 switch ($name) {
 case "h1":
 case "h2":
 case "h3":
 case "h4":
 case "h5":
 case "h6":
 case "pre":
 case "p":
 // add two lines
 $output .= "\n\n";
 break;
 case "br":
 // add one line
 $output .= "\n";
 break;
 case "div":
 break;
 case "a":
 // links are returned in [text](link) format
 $href = $node->getAttribute("href");
 $output = \trim($output);
 // remove double [[ ]] s from linking images
 if (\substr($output, 0, 1) == "[" && \substr($output, -1) == "]") {
 $output = \substr($output, 1, \strlen($output) - 2);
 // for linking images, the title of the <a> overrides the title of the <img>
 if ($node->getAttribute("title")) {
 $output = $node->getAttribute("title");
 }
 }
 // if there is no link text, but a title attr
 if (!$output && $node->getAttribute("title")) {
 $output = $node->getAttribute("title");
 }
 if ($href == null) {
 // it doesn't link anywhere
 if ($node->getAttribute("name") != null) {
 $output = "[{$output}]";
 }
 } else {
 if ($output) {
 $output = "[{$output}]({$href})";
 } else {
 // empty string
 $output = $href;
 }
 }
 // does the next node require additional whitespace?
 switch ($nextName) {
 case "h1":
 case "h2":
 case "h3":
 case "h4":
 case "h5":
 case "h6":
 $output .= "\n";
 break;
 }
 break;
 case "img":
 if ($node->getAttribute("title")) {
 $output = "[" . $node->getAttribute("title") . "]";
 } elseif ($node->getAttribute("alt")) {
 $output = "[" . $node->getAttribute("alt") . "]";
 } else {
 $output = "";
 }
 break;
 case "li":
 $output .= "\n";
 break;
 case "blockquote":
 // process quoted text for whitespace/newlines
 $output = static::processWhitespaceNewlines($output);
 // add leading newline
 $output = "\n" . $output;
 // prepend '> ' at the beginning of all lines
 $output = \preg_replace("/\n/im", "\n> ", $output);
 // replace leading '> >' with '>>'
 $output = \preg_replace("/\n> >/im", "\n>>", $output);
 // add another leading newline and trailing newlines
 $output = "\n" . $output . "\n\n";
 break;
 default:
 }
 return $output;
 }
}
