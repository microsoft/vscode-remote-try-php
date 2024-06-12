<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Exceptions\InvalidCastException;
use MailPoetVendor\Carbon\Exceptions\InvalidTimeZoneException;
use DateTimeInterface;
use DateTimeZone;
use Throwable;
class CarbonTimeZone extends DateTimeZone
{
 public function __construct($timezone = null)
 {
 parent::__construct(static::getDateTimeZoneNameFromMixed($timezone));
 }
 protected static function parseNumericTimezone($timezone)
 {
 if ($timezone <= -100 || $timezone >= 100) {
 throw new InvalidTimeZoneException('Absolute timezone offset cannot be greater than 100.');
 }
 return ($timezone >= 0 ? '+' : '') . \ltrim($timezone, '+') . ':00';
 }
 protected static function getDateTimeZoneNameFromMixed($timezone)
 {
 if ($timezone === null) {
 return \date_default_timezone_get();
 }
 if (\is_string($timezone)) {
 $timezone = \preg_replace('/^\\s*([+-]\\d+)(\\d{2})\\s*$/', '$1:$2', $timezone);
 }
 if (\is_numeric($timezone)) {
 return static::parseNumericTimezone($timezone);
 }
 return $timezone;
 }
 protected static function getDateTimeZoneFromName(&$name)
 {
 return @\timezone_open($name = (string) static::getDateTimeZoneNameFromMixed($name));
 }
 public function cast(string $className)
 {
 if (!\method_exists($className, 'instance')) {
 if (\is_a($className, DateTimeZone::class, \true)) {
 return new $className($this->getName());
 }
 throw new InvalidCastException("{$className} has not the instance() method needed to cast the date.");
 }
 return $className::instance($this);
 }
 public static function instance($object = null, $objectDump = null)
 {
 $tz = $object;
 if ($tz instanceof static) {
 return $tz;
 }
 if ($tz === null) {
 return new static();
 }
 if (!$tz instanceof DateTimeZone) {
 $tz = static::getDateTimeZoneFromName($object);
 }
 if ($tz !== \false) {
 return new static($tz->getName());
 }
 if (Carbon::isStrictModeEnabled()) {
 throw new InvalidTimeZoneException('Unknown or bad timezone (' . ($objectDump ?: $object) . ')');
 }
 return \false;
 }
 public function getAbbreviatedName($dst = \false)
 {
 $name = $this->getName();
 foreach ($this->listAbbreviations() as $abbreviation => $zones) {
 foreach ($zones as $zone) {
 if ($zone['timezone_id'] === $name && $zone['dst'] == $dst) {
 return $abbreviation;
 }
 }
 }
 return 'unknown';
 }
 public function getAbbr($dst = \false)
 {
 return $this->getAbbreviatedName($dst);
 }
 public function toOffsetName(DateTimeInterface $date = null)
 {
 return static::getOffsetNameFromMinuteOffset($this->getOffset($date ?: Carbon::now($this)) / 60);
 }
 public function toOffsetTimeZone(DateTimeInterface $date = null)
 {
 return new static($this->toOffsetName($date));
 }
 public function toRegionName(DateTimeInterface $date = null, $isDst = 1)
 {
 $name = $this->getName();
 $firstChar = \substr($name, 0, 1);
 if ($firstChar !== '+' && $firstChar !== '-') {
 return $name;
 }
 $date = $date ?: Carbon::now($this);
 // Integer construction no longer supported since PHP 8
 // @codeCoverageIgnoreStart
 try {
 $offset = @$this->getOffset($date) ?: 0;
 } catch (Throwable $e) {
 $offset = 0;
 }
 // @codeCoverageIgnoreEnd
 $name = @\timezone_name_from_abbr('', $offset, $isDst);
 if ($name) {
 return $name;
 }
 foreach (\timezone_identifiers_list() as $timezone) {
 if (Carbon::instance($date)->tz($timezone)->getOffset() === $offset) {
 return $timezone;
 }
 }
 return \false;
 }
 public function toRegionTimeZone(DateTimeInterface $date = null)
 {
 $tz = $this->toRegionName($date);
 if ($tz !== \false) {
 return new static($tz);
 }
 if (Carbon::isStrictModeEnabled()) {
 throw new InvalidTimeZoneException('Unknown timezone for offset ' . $this->getOffset($date ?: Carbon::now($this)) . ' seconds.');
 }
 return \false;
 }
 public function __toString()
 {
 return $this->getName();
 }
 public function getType() : int
 {
 return \preg_match('/"timezone_type";i:(\\d)/', \serialize($this), $match) ? (int) $match[1] : 3;
 }
 public static function create($object = null)
 {
 return static::instance($object);
 }
 public static function createFromHourOffset(float $hourOffset)
 {
 return static::createFromMinuteOffset($hourOffset * Carbon::MINUTES_PER_HOUR);
 }
 public static function createFromMinuteOffset(float $minuteOffset)
 {
 return static::instance(static::getOffsetNameFromMinuteOffset($minuteOffset));
 }
 public static function getOffsetNameFromMinuteOffset(float $minutes) : string
 {
 $minutes = \round($minutes);
 $unsignedMinutes = \abs($minutes);
 return ($minutes < 0 ? '-' : '+') . \str_pad((string) \floor($unsignedMinutes / 60), 2, '0', \STR_PAD_LEFT) . ':' . \str_pad((string) ($unsignedMinutes % 60), 2, '0', \STR_PAD_LEFT);
 }
}
