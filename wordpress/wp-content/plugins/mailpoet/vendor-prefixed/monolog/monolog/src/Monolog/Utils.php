<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog;
if (!defined('ABSPATH')) exit;
final class Utils
{
 const DEFAULT_JSON_FLAGS = \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_PRESERVE_ZERO_FRACTION | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_PARTIAL_OUTPUT_ON_ERROR;
 public static function getClass(object $object) : string
 {
 $class = \get_class($object);
 if (\false === ($pos = \strpos($class, "@anonymous\x00"))) {
 return $class;
 }
 if (\false === ($parent = \get_parent_class($class))) {
 return \substr($class, 0, $pos + 10);
 }
 return $parent . '@anonymous';
 }
 public static function substr(string $string, int $start, ?int $length = null) : string
 {
 if (\extension_loaded('mbstring')) {
 return \mb_strcut($string, $start, $length);
 }
 return \substr($string, $start, null === $length ? \strlen($string) : $length);
 }
 public static function canonicalizePath(string $streamUrl) : string
 {
 $prefix = '';
 if ('file://' === \substr($streamUrl, 0, 7)) {
 $streamUrl = \substr($streamUrl, 7);
 $prefix = 'file://';
 }
 // other type of stream, not supported
 if (\false !== \strpos($streamUrl, '://')) {
 return $streamUrl;
 }
 // already absolute
 if (\substr($streamUrl, 0, 1) === '/' || \substr($streamUrl, 1, 1) === ':' || \substr($streamUrl, 0, 2) === '\\\\') {
 return $prefix . $streamUrl;
 }
 $streamUrl = \getcwd() . '/' . $streamUrl;
 return $prefix . $streamUrl;
 }
 public static function jsonEncode($data, ?int $encodeFlags = null, bool $ignoreErrors = \false) : string
 {
 if (null === $encodeFlags) {
 $encodeFlags = self::DEFAULT_JSON_FLAGS;
 }
 if ($ignoreErrors) {
 $json = @\json_encode($data, $encodeFlags);
 if (\false === $json) {
 return 'null';
 }
 return $json;
 }
 $json = \json_encode($data, $encodeFlags);
 if (\false === $json) {
 $json = self::handleJsonError(\json_last_error(), $data);
 }
 return $json;
 }
 public static function handleJsonError(int $code, $data, ?int $encodeFlags = null) : string
 {
 if ($code !== \JSON_ERROR_UTF8) {
 self::throwEncodeError($code, $data);
 }
 if (\is_string($data)) {
 self::detectAndCleanUtf8($data);
 } elseif (\is_array($data)) {
 \array_walk_recursive($data, array('Monolog\\Utils', 'detectAndCleanUtf8'));
 } else {
 self::throwEncodeError($code, $data);
 }
 if (null === $encodeFlags) {
 $encodeFlags = self::DEFAULT_JSON_FLAGS;
 }
 $json = \json_encode($data, $encodeFlags);
 if ($json === \false) {
 self::throwEncodeError(\json_last_error(), $data);
 }
 return $json;
 }
 public static function pcreLastErrorMessage(int $code) : string
 {
 if (\PHP_VERSION_ID >= 80000) {
 return \preg_last_error_msg();
 }
 $constants = \get_defined_constants(\true)['pcre'];
 $constants = \array_filter($constants, function ($key) {
 return \substr($key, -6) == '_ERROR';
 }, \ARRAY_FILTER_USE_KEY);
 $constants = \array_flip($constants);
 return $constants[$code] ?? 'UNDEFINED_ERROR';
 }
 private static function throwEncodeError(int $code, $data) : void
 {
 switch ($code) {
 case \JSON_ERROR_DEPTH:
 $msg = 'Maximum stack depth exceeded';
 break;
 case \JSON_ERROR_STATE_MISMATCH:
 $msg = 'Underflow or the modes mismatch';
 break;
 case \JSON_ERROR_CTRL_CHAR:
 $msg = 'Unexpected control character found';
 break;
 case \JSON_ERROR_UTF8:
 $msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
 break;
 default:
 $msg = 'Unknown error';
 }
 throw new \RuntimeException('JSON encoding failed: ' . $msg . '. Encoding: ' . \var_export($data, \true));
 }
 private static function detectAndCleanUtf8(&$data) : void
 {
 if (\is_string($data) && !\preg_match('//u', $data)) {
 $data = \preg_replace_callback('/[\\x80-\\xFF]+/', function ($m) {
 return \function_exists('mb_convert_encoding') ? \mb_convert_encoding($m[0], 'UTF-8', 'ISO-8859-1') : \utf8_encode($m[0]);
 }, $data);
 if (!\is_string($data)) {
 $pcreErrorCode = \preg_last_error();
 throw new \RuntimeException('Failed to preg_replace_callback: ' . $pcreErrorCode . ' / ' . self::pcreLastErrorMessage($pcreErrorCode));
 }
 $data = \str_replace(['¤', '¦', '¨', '´', '¸', '¼', '½', '¾'], ['€', 'Š', 'š', 'Ž', 'ž', 'Œ', 'œ', 'Ÿ'], $data);
 }
 }
 public static function expandIniShorthandBytes($val)
 {
 if (!\is_string($val)) {
 return \false;
 }
 // support -1
 if ((int) $val < 0) {
 return (int) $val;
 }
 if (!\preg_match('/^\\s*(?<val>\\d+)(?:\\.\\d+)?\\s*(?<unit>[gmk]?)\\s*$/i', $val, $match)) {
 return \false;
 }
 $val = (int) $match['val'];
 switch (\strtolower($match['unit'] ?? '')) {
 case 'g':
 $val *= 1024;
 case 'm':
 $val *= 1024;
 case 'k':
 $val *= 1024;
 }
 return $val;
 }
 public static function getRecordMessageForException(array $record) : string
 {
 $context = '';
 $extra = '';
 try {
 if ($record['context']) {
 $context = "\nContext: " . \json_encode($record['context']);
 }
 if ($record['extra']) {
 $extra = "\nExtra: " . \json_encode($record['extra']);
 }
 } catch (\Throwable $e) {
 // noop
 }
 return "\nThe exception occurred while attempting to log: " . $record['message'] . $context . $extra;
 }
}
