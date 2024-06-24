<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
trait Timestamp
{
 public static function createFromTimestamp($timestamp, $tz = null)
 {
 return static::createFromTimestampUTC($timestamp)->setTimezone($tz);
 }
 public static function createFromTimestampUTC($timestamp)
 {
 [$integer, $decimal] = self::getIntegerAndDecimalParts($timestamp);
 $delta = \floor($decimal / static::MICROSECONDS_PER_SECOND);
 $integer += $delta;
 $decimal -= $delta * static::MICROSECONDS_PER_SECOND;
 $decimal = \str_pad((string) $decimal, 6, '0', \STR_PAD_LEFT);
 return static::rawCreateFromFormat('U u', "{$integer} {$decimal}");
 }
 public static function createFromTimestampMsUTC($timestamp)
 {
 [$milliseconds, $microseconds] = self::getIntegerAndDecimalParts($timestamp, 3);
 $sign = $milliseconds < 0 || $milliseconds === 0.0 && $microseconds < 0 ? -1 : 1;
 $milliseconds = \abs($milliseconds);
 $microseconds = $sign * \abs($microseconds) + static::MICROSECONDS_PER_MILLISECOND * ($milliseconds % static::MILLISECONDS_PER_SECOND);
 $seconds = $sign * \floor($milliseconds / static::MILLISECONDS_PER_SECOND);
 $delta = \floor($microseconds / static::MICROSECONDS_PER_SECOND);
 $seconds += $delta;
 $microseconds -= $delta * static::MICROSECONDS_PER_SECOND;
 $microseconds = \str_pad($microseconds, 6, '0', \STR_PAD_LEFT);
 return static::rawCreateFromFormat('U u', "{$seconds} {$microseconds}");
 }
 public static function createFromTimestampMs($timestamp, $tz = null)
 {
 return static::createFromTimestampMsUTC($timestamp)->setTimezone($tz);
 }
 public function timestamp($unixTimestamp)
 {
 return $this->setTimestamp($unixTimestamp);
 }
 public function getPreciseTimestamp($precision = 6)
 {
 return \round((float) $this->rawFormat('Uu') / \pow(10, 6 - $precision));
 }
 public function valueOf()
 {
 return $this->getPreciseTimestamp(3);
 }
 public function getTimestampMs()
 {
 return (int) $this->getPreciseTimestamp(3);
 }
 public function unix()
 {
 return $this->getTimestamp();
 }
 private static function getIntegerAndDecimalParts($numbers, $decimals = 6)
 {
 if (\is_int($numbers) || \is_float($numbers)) {
 $numbers = \number_format($numbers, $decimals, '.', '');
 }
 $sign = \str_starts_with($numbers, '-') ? -1 : 1;
 $integer = 0;
 $decimal = 0;
 foreach (\preg_split('`[^\\d.]+`', $numbers) as $chunk) {
 [$integerPart, $decimalPart] = \explode('.', "{$chunk}.");
 $integer += (int) $integerPart;
 $decimal += (float) "0.{$decimalPart}";
 }
 $overflow = \floor($decimal);
 $integer += $overflow;
 $decimal -= $overflow;
 return [$sign * $integer, $decimal === 0.0 ? 0.0 : $sign * \round($decimal * \pow(10, $decimals))];
 }
}
