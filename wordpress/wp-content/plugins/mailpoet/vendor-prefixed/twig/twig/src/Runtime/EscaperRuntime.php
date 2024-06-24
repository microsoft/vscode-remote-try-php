<?php
namespace MailPoetVendor\Twig\Runtime;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\RuntimeExtensionInterface;
use MailPoetVendor\Twig\Markup;
final class EscaperRuntime implements RuntimeExtensionInterface
{
 private $escapers = [];
 public $safeClasses = [];
 public $safeLookup = [];
 private $charset;
 public function __construct($charset = 'UTF-8')
 {
 $this->charset = $charset;
 }
 public function setEscaper($strategy, callable $callable)
 {
 $this->escapers[$strategy] = $callable;
 }
 public function getEscapers()
 {
 return $this->escapers;
 }
 public function setSafeClasses(array $safeClasses = [])
 {
 $this->safeClasses = [];
 $this->safeLookup = [];
 foreach ($safeClasses as $class => $strategies) {
 $this->addSafeClass($class, $strategies);
 }
 }
 public function addSafeClass(string $class, array $strategies)
 {
 $class = \ltrim($class, '\\');
 if (!isset($this->safeClasses[$class])) {
 $this->safeClasses[$class] = [];
 }
 $this->safeClasses[$class] = \array_merge($this->safeClasses[$class], $strategies);
 foreach ($strategies as $strategy) {
 $this->safeLookup[$strategy][$class] = \true;
 }
 }
 public function escape($string, string $strategy = 'html', ?string $charset = null, bool $autoescape = \false)
 {
 if ($autoescape && $string instanceof Markup) {
 return $string;
 }
 if (!\is_string($string)) {
 if (\is_object($string) && \method_exists($string, '__toString')) {
 if ($autoescape) {
 $c = \get_class($string);
 if (!isset($this->safeClasses[$c])) {
 $this->safeClasses[$c] = [];
 foreach (\class_parents($string) + \class_implements($string) as $class) {
 if (isset($this->safeClasses[$class])) {
 $this->safeClasses[$c] = \array_unique(\array_merge($this->safeClasses[$c], $this->safeClasses[$class]));
 foreach ($this->safeClasses[$class] as $s) {
 $this->safeLookup[$s][$c] = \true;
 }
 }
 }
 }
 if (isset($this->safeLookup[$strategy][$c]) || isset($this->safeLookup['all'][$c])) {
 return (string) $string;
 }
 }
 $string = (string) $string;
 } elseif (\in_array($strategy, ['html', 'js', 'css', 'html_attr', 'url'])) {
 // we return the input as is (which can be of any type)
 return $string;
 }
 }
 if ('' === $string) {
 return '';
 }
 $charset = $charset ?: $this->charset;
 switch ($strategy) {
 case 'html':
 // see https://www.php.net/htmlspecialchars
 // Using a static variable to avoid initializing the array
 // each time the function is called. Moving the declaration on the
 // top of the function slow downs other escaping strategies.
 static $htmlspecialcharsCharsets = ['ISO-8859-1' => \true, 'ISO8859-1' => \true, 'ISO-8859-15' => \true, 'ISO8859-15' => \true, 'utf-8' => \true, 'UTF-8' => \true, 'CP866' => \true, 'IBM866' => \true, '866' => \true, 'CP1251' => \true, 'WINDOWS-1251' => \true, 'WIN-1251' => \true, '1251' => \true, 'CP1252' => \true, 'WINDOWS-1252' => \true, '1252' => \true, 'KOI8-R' => \true, 'KOI8-RU' => \true, 'KOI8R' => \true, 'BIG5' => \true, '950' => \true, 'GB2312' => \true, '936' => \true, 'BIG5-HKSCS' => \true, 'SHIFT_JIS' => \true, 'SJIS' => \true, '932' => \true, 'EUC-JP' => \true, 'EUCJP' => \true, 'ISO8859-5' => \true, 'ISO-8859-5' => \true, 'MACROMAN' => \true];
 if (isset($htmlspecialcharsCharsets[$charset])) {
 return \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, $charset);
 }
 if (isset($htmlspecialcharsCharsets[\strtoupper($charset)])) {
 // cache the lowercase variant for future iterations
 $htmlspecialcharsCharsets[$charset] = \true;
 return \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, $charset);
 }
 $string = $this->convertEncoding($string, 'UTF-8', $charset);
 $string = \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
 return \iconv('UTF-8', $charset, $string);
 case 'js':
 // escape all non-alphanumeric characters
 // into their \x or \uHHHH representations
 if ('UTF-8' !== $charset) {
 $string = $this->convertEncoding($string, 'UTF-8', $charset);
 }
 if (!\preg_match('//u', $string)) {
 throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
 }
 $string = \preg_replace_callback('#[^a-zA-Z0-9,\\._]#Su', function ($matches) {
 $char = $matches[0];
 static $shortMap = ['\\' => '\\\\', '/' => '\\/', "\x08" => '\\b', "\f" => '\\f', "\n" => '\\n', "\r" => '\\r', "\t" => '\\t'];
 if (isset($shortMap[$char])) {
 return $shortMap[$char];
 }
 $codepoint = \mb_ord($char, 'UTF-8');
 if (0x10000 > $codepoint) {
 return \sprintf('\\u%04X', $codepoint);
 }
 // Split characters outside the BMP into surrogate pairs
 // https://tools.ietf.org/html/rfc2781.html#section-2.1
 $u = $codepoint - 0x10000;
 $high = 0xd800 | $u >> 10;
 $low = 0xdc00 | $u & 0x3ff;
 return \sprintf('\\u%04X\\u%04X', $high, $low);
 }, $string);
 if ('UTF-8' !== $charset) {
 $string = \iconv('UTF-8', $charset, $string);
 }
 return $string;
 case 'css':
 if ('UTF-8' !== $charset) {
 $string = $this->convertEncoding($string, 'UTF-8', $charset);
 }
 if (!\preg_match('//u', $string)) {
 throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
 }
 $string = \preg_replace_callback('#[^a-zA-Z0-9]#Su', function ($matches) {
 $char = $matches[0];
 return \sprintf('\\%X ', 1 === \strlen($char) ? \ord($char) : \mb_ord($char, 'UTF-8'));
 }, $string);
 if ('UTF-8' !== $charset) {
 $string = \iconv('UTF-8', $charset, $string);
 }
 return $string;
 case 'html_attr':
 if ('UTF-8' !== $charset) {
 $string = $this->convertEncoding($string, 'UTF-8', $charset);
 }
 if (!\preg_match('//u', $string)) {
 throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
 }
 $string = \preg_replace_callback('#[^a-zA-Z0-9,\\.\\-_]#Su', function ($matches) {
 $chr = $matches[0];
 $ord = \ord($chr);
 if ($ord <= 0x1f && "\t" != $chr && "\n" != $chr && "\r" != $chr || $ord >= 0x7f && $ord <= 0x9f) {
 return '&#xFFFD;';
 }
 if (1 === \strlen($chr)) {
 static $entityMap = [
 34 => '&quot;',
 38 => '&amp;',
 60 => '&lt;',
 62 => '&gt;',
 ];
 if (isset($entityMap[$ord])) {
 return $entityMap[$ord];
 }
 return \sprintf('&#x%02X;', $ord);
 }
 return \sprintf('&#x%04X;', \mb_ord($chr, 'UTF-8'));
 }, $string);
 if ('UTF-8' !== $charset) {
 $string = \iconv('UTF-8', $charset, $string);
 }
 return $string;
 case 'url':
 return \rawurlencode($string);
 default:
 if (\array_key_exists($strategy, $this->escapers)) {
 return $this->escapers[$strategy]($string, $charset);
 }
 $validStrategies = \implode('", "', \array_merge(['html', 'js', 'url', 'css', 'html_attr'], \array_keys($this->escapers)));
 throw new RuntimeError(\sprintf('Invalid escaping strategy "%s" (valid ones: "%s").', $strategy, $validStrategies));
 }
 }
 private function convertEncoding(string $string, string $to, string $from)
 {
 if (!\function_exists('iconv')) {
 throw new RuntimeError('Unable to convert encoding: required function iconv() does not exist. You should install ext-iconv or symfony/polyfill-iconv.');
 }
 return \iconv($from, $to, $string);
 }
}
