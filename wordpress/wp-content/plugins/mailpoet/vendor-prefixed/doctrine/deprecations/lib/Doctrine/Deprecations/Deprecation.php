<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\Deprecations;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Log\LoggerInterface;
use function array_key_exists;
use function array_reduce;
use function debug_backtrace;
use function sprintf;
use function strpos;
use function strrpos;
use function substr;
use function trigger_error;
use const DEBUG_BACKTRACE_IGNORE_ARGS;
use const DIRECTORY_SEPARATOR;
use const E_USER_DEPRECATED;
class Deprecation
{
 private const TYPE_NONE = 0;
 private const TYPE_TRACK_DEPRECATIONS = 1;
 private const TYPE_TRIGGER_ERROR = 2;
 private const TYPE_PSR_LOGGER = 4;
 private static $type = self::TYPE_NONE;
 private static $logger;
 private static $ignoredPackages = [];
 private static $ignoredLinks = [];
 private static $deduplication = \true;
 public static function trigger(string $package, string $link, string $message, ...$args) : void
 {
 if (self::$type === self::TYPE_NONE) {
 return;
 }
 if (array_key_exists($link, self::$ignoredLinks)) {
 self::$ignoredLinks[$link]++;
 } else {
 self::$ignoredLinks[$link] = 1;
 }
 if (self::$deduplication === \true && self::$ignoredLinks[$link] > 1) {
 return;
 }
 if (isset(self::$ignoredPackages[$package])) {
 return;
 }
 $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
 $message = sprintf($message, ...$args);
 self::delegateTriggerToBackend($message, $backtrace, $link, $package);
 }
 public static function triggerIfCalledFromOutside(string $package, string $link, string $message, ...$args) : void
 {
 if (self::$type === self::TYPE_NONE) {
 return;
 }
 $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
 // first check that the caller is not from a tests folder, in which case we always let deprecations pass
 if (strpos($backtrace[1]['file'], DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR) === \false) {
 $path = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR;
 if (strpos($backtrace[0]['file'], $path) === \false) {
 return;
 }
 if (strpos($backtrace[1]['file'], $path) !== \false) {
 return;
 }
 }
 if (array_key_exists($link, self::$ignoredLinks)) {
 self::$ignoredLinks[$link]++;
 } else {
 self::$ignoredLinks[$link] = 1;
 }
 if (self::$deduplication === \true && self::$ignoredLinks[$link] > 1) {
 return;
 }
 if (isset(self::$ignoredPackages[$package])) {
 return;
 }
 $message = sprintf($message, ...$args);
 self::delegateTriggerToBackend($message, $backtrace, $link, $package);
 }
 private static function delegateTriggerToBackend(string $message, array $backtrace, string $link, string $package) : void
 {
 if ((self::$type & self::TYPE_PSR_LOGGER) > 0) {
 $context = ['file' => $backtrace[0]['file'], 'line' => $backtrace[0]['line'], 'package' => $package, 'link' => $link];
 self::$logger->notice($message, $context);
 }
 if (!((self::$type & self::TYPE_TRIGGER_ERROR) > 0)) {
 return;
 }
 $message .= sprintf(' (%s:%d called by %s:%d, %s, package %s)', self::basename($backtrace[0]['file']), $backtrace[0]['line'], self::basename($backtrace[1]['file']), $backtrace[1]['line'], $link, $package);
 @trigger_error($message, E_USER_DEPRECATED);
 }
 private static function basename(string $filename) : string
 {
 $pos = strrpos($filename, DIRECTORY_SEPARATOR);
 if ($pos === \false) {
 return $filename;
 }
 return substr($filename, $pos + 1);
 }
 public static function enableTrackingDeprecations() : void
 {
 self::$type |= self::TYPE_TRACK_DEPRECATIONS;
 }
 public static function enableWithTriggerError() : void
 {
 self::$type |= self::TYPE_TRIGGER_ERROR;
 }
 public static function enableWithPsrLogger(LoggerInterface $logger) : void
 {
 self::$type |= self::TYPE_PSR_LOGGER;
 self::$logger = $logger;
 }
 public static function withoutDeduplication() : void
 {
 self::$deduplication = \false;
 }
 public static function disable() : void
 {
 self::$type = self::TYPE_NONE;
 self::$logger = null;
 self::$deduplication = \true;
 foreach (self::$ignoredLinks as $link => $count) {
 self::$ignoredLinks[$link] = 0;
 }
 }
 public static function ignorePackage(string $packageName) : void
 {
 self::$ignoredPackages[$packageName] = \true;
 }
 public static function ignoreDeprecations(string ...$links) : void
 {
 foreach ($links as $link) {
 self::$ignoredLinks[$link] = 0;
 }
 }
 public static function getUniqueTriggeredDeprecationsCount() : int
 {
 return array_reduce(self::$ignoredLinks, static function (int $carry, int $count) {
 return $carry + $count;
 }, 0);
 }
 public static function getTriggeredDeprecations() : array
 {
 return self::$ignoredLinks;
 }
}
