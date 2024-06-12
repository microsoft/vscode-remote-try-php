<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Carbon\CarbonInterface;
use MailPoetVendor\Carbon\CarbonInterval;
use MailPoetVendor\Carbon\CarbonPeriod;
use MailPoetVendor\Carbon\Exceptions\UnitException;
use Closure;
use DateTime;
use DateTimeImmutable;
use ReturnTypeWillChange;
trait Converter
{
 use ToStringFormat;
 #[\ReturnTypeWillChange]
 public function format($format)
 {
 $function = $this->localFormatFunction ?: static::$formatFunction;
 if (!$function) {
 return $this->rawFormat($format);
 }
 if (\is_string($function) && \method_exists($this, $function)) {
 $function = [$this, $function];
 }
 return $function(...\func_get_args());
 }
 public function rawFormat($format)
 {
 return parent::format($format);
 }
 public function __toString()
 {
 $format = $this->localToStringFormat ?? static::$toStringFormat;
 return $format instanceof Closure ? $format($this) : $this->rawFormat($format ?: (\defined('static::DEFAULT_TO_STRING_FORMAT') ? static::DEFAULT_TO_STRING_FORMAT : CarbonInterface::DEFAULT_TO_STRING_FORMAT));
 }
 public function toDateString()
 {
 return $this->rawFormat('Y-m-d');
 }
 public function toFormattedDateString()
 {
 return $this->rawFormat('M j, Y');
 }
 public function toFormattedDayDateString() : string
 {
 return $this->rawFormat('D, M j, Y');
 }
 public function toTimeString($unitPrecision = 'second')
 {
 return $this->rawFormat(static::getTimeFormatByPrecision($unitPrecision));
 }
 public function toDateTimeString($unitPrecision = 'second')
 {
 return $this->rawFormat('Y-m-d ' . static::getTimeFormatByPrecision($unitPrecision));
 }
 public static function getTimeFormatByPrecision($unitPrecision)
 {
 switch (static::singularUnit($unitPrecision)) {
 case 'minute':
 return 'H:i';
 case 'second':
 return 'H:i:s';
 case 'm':
 case 'millisecond':
 return 'H:i:s.v';
 case 'µ':
 case 'microsecond':
 return 'H:i:s.u';
 }
 throw new UnitException('Precision unit expected among: minute, second, millisecond and microsecond.');
 }
 public function toDateTimeLocalString($unitPrecision = 'second')
 {
 return $this->rawFormat('Y-m-d\\T' . static::getTimeFormatByPrecision($unitPrecision));
 }
 public function toDayDateTimeString()
 {
 return $this->rawFormat('D, M j, Y g:i A');
 }
 public function toAtomString()
 {
 return $this->rawFormat(DateTime::ATOM);
 }
 public function toCookieString()
 {
 return $this->rawFormat(DateTime::COOKIE);
 }
 public function toIso8601String()
 {
 return $this->toAtomString();
 }
 public function toRfc822String()
 {
 return $this->rawFormat(DateTime::RFC822);
 }
 public function toIso8601ZuluString($unitPrecision = 'second')
 {
 return $this->avoidMutation()->utc()->rawFormat('Y-m-d\\T' . static::getTimeFormatByPrecision($unitPrecision) . '\\Z');
 }
 public function toRfc850String()
 {
 return $this->rawFormat(DateTime::RFC850);
 }
 public function toRfc1036String()
 {
 return $this->rawFormat(DateTime::RFC1036);
 }
 public function toRfc1123String()
 {
 return $this->rawFormat(DateTime::RFC1123);
 }
 public function toRfc2822String()
 {
 return $this->rawFormat(DateTime::RFC2822);
 }
 public function toRfc3339String($extended = \false)
 {
 $format = DateTime::RFC3339;
 if ($extended) {
 $format = DateTime::RFC3339_EXTENDED;
 }
 return $this->rawFormat($format);
 }
 public function toRssString()
 {
 return $this->rawFormat(DateTime::RSS);
 }
 public function toW3cString()
 {
 return $this->rawFormat(DateTime::W3C);
 }
 public function toRfc7231String()
 {
 return $this->avoidMutation()->setTimezone('GMT')->rawFormat(\defined('static::RFC7231_FORMAT') ? static::RFC7231_FORMAT : CarbonInterface::RFC7231_FORMAT);
 }
 public function toArray()
 {
 return ['year' => $this->year, 'month' => $this->month, 'day' => $this->day, 'dayOfWeek' => $this->dayOfWeek, 'dayOfYear' => $this->dayOfYear, 'hour' => $this->hour, 'minute' => $this->minute, 'second' => $this->second, 'micro' => $this->micro, 'timestamp' => $this->timestamp, 'formatted' => $this->rawFormat(\defined('static::DEFAULT_TO_STRING_FORMAT') ? static::DEFAULT_TO_STRING_FORMAT : CarbonInterface::DEFAULT_TO_STRING_FORMAT), 'timezone' => $this->timezone];
 }
 public function toObject()
 {
 return (object) $this->toArray();
 }
 public function toString()
 {
 return $this->avoidMutation()->locale('en')->isoFormat('ddd MMM DD YYYY HH:mm:ss [GMT]ZZ');
 }
 public function toISOString($keepOffset = \false)
 {
 if (!$this->isValid()) {
 return null;
 }
 $yearFormat = $this->year < 0 || $this->year > 9999 ? 'YYYYYY' : 'YYYY';
 $tzFormat = $keepOffset ? 'Z' : '[Z]';
 $date = $keepOffset ? $this : $this->avoidMutation()->utc();
 return $date->isoFormat("{$yearFormat}-MM-DD[T]HH:mm:ss.SSSSSS{$tzFormat}");
 }
 public function toJSON()
 {
 return $this->toISOString();
 }
 public function toDateTime()
 {
 return new DateTime($this->rawFormat('Y-m-d H:i:s.u'), $this->getTimezone());
 }
 public function toDateTimeImmutable()
 {
 return new DateTimeImmutable($this->rawFormat('Y-m-d H:i:s.u'), $this->getTimezone());
 }
 public function toDate()
 {
 return $this->toDateTime();
 }
 public function toPeriod($end = null, $interval = null, $unit = null)
 {
 if ($unit) {
 $interval = CarbonInterval::make("{$interval} " . static::pluralUnit($unit));
 }
 $period = (new CarbonPeriod())->setDateClass(static::class)->setStartDate($this);
 if ($interval) {
 $period->setDateInterval($interval);
 }
 if (\is_int($end) || \is_string($end) && \ctype_digit($end)) {
 $period->setRecurrences($end);
 } elseif ($end) {
 $period->setEndDate($end);
 }
 return $period;
 }
 public function range($end = null, $interval = null, $unit = null)
 {
 return $this->toPeriod($end, $interval, $unit);
 }
}
