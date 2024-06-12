<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use BadMethodCallException;
use MailPoetVendor\Carbon\CarbonInterface;
use MailPoetVendor\Carbon\Exceptions\BadComparisonUnitException;
use InvalidArgumentException;
trait Comparison
{
 protected $endOfTime = \false;
 protected $startOfTime = \false;
 public function eq($date) : bool
 {
 return $this->equalTo($date);
 }
 public function equalTo($date) : bool
 {
 $this->discourageNull($date);
 $this->discourageBoolean($date);
 return $this == $this->resolveCarbon($date);
 }
 public function ne($date) : bool
 {
 return $this->notEqualTo($date);
 }
 public function notEqualTo($date) : bool
 {
 return !$this->equalTo($date);
 }
 public function gt($date) : bool
 {
 return $this->greaterThan($date);
 }
 public function greaterThan($date) : bool
 {
 $this->discourageNull($date);
 $this->discourageBoolean($date);
 return $this > $this->resolveCarbon($date);
 }
 public function isAfter($date) : bool
 {
 return $this->greaterThan($date);
 }
 public function gte($date) : bool
 {
 return $this->greaterThanOrEqualTo($date);
 }
 public function greaterThanOrEqualTo($date) : bool
 {
 $this->discourageNull($date);
 $this->discourageBoolean($date);
 return $this >= $this->resolveCarbon($date);
 }
 public function lt($date) : bool
 {
 return $this->lessThan($date);
 }
 public function lessThan($date) : bool
 {
 $this->discourageNull($date);
 $this->discourageBoolean($date);
 return $this < $this->resolveCarbon($date);
 }
 public function isBefore($date) : bool
 {
 return $this->lessThan($date);
 }
 public function lte($date) : bool
 {
 return $this->lessThanOrEqualTo($date);
 }
 public function lessThanOrEqualTo($date) : bool
 {
 $this->discourageNull($date);
 $this->discourageBoolean($date);
 return $this <= $this->resolveCarbon($date);
 }
 public function between($date1, $date2, $equal = \true) : bool
 {
 $date1 = $this->resolveCarbon($date1);
 $date2 = $this->resolveCarbon($date2);
 if ($date1->greaterThan($date2)) {
 [$date1, $date2] = [$date2, $date1];
 }
 if ($equal) {
 return $this >= $date1 && $this <= $date2;
 }
 return $this > $date1 && $this < $date2;
 }
 public function betweenIncluded($date1, $date2) : bool
 {
 return $this->between($date1, $date2, \true);
 }
 public function betweenExcluded($date1, $date2) : bool
 {
 return $this->between($date1, $date2, \false);
 }
 public function isBetween($date1, $date2, $equal = \true) : bool
 {
 return $this->between($date1, $date2, $equal);
 }
 public function isWeekday()
 {
 return !$this->isWeekend();
 }
 public function isWeekend()
 {
 return \in_array($this->dayOfWeek, static::$weekendDays, \true);
 }
 public function isYesterday()
 {
 return $this->toDateString() === static::yesterday($this->getTimezone())->toDateString();
 }
 public function isToday()
 {
 return $this->toDateString() === $this->nowWithSameTz()->toDateString();
 }
 public function isTomorrow()
 {
 return $this->toDateString() === static::tomorrow($this->getTimezone())->toDateString();
 }
 public function isFuture()
 {
 return $this->greaterThan($this->nowWithSameTz());
 }
 public function isPast()
 {
 return $this->lessThan($this->nowWithSameTz());
 }
 public function isLeapYear()
 {
 return $this->rawFormat('L') === '1';
 }
 public function isLongYear()
 {
 return static::create($this->year, 12, 28, 0, 0, 0, $this->tz)->weekOfYear === 53;
 }
 public function isSameAs($format, $date = null)
 {
 return $this->rawFormat($format) === $this->resolveCarbon($date)->rawFormat($format);
 }
 public function isSameUnit($unit, $date = null)
 {
 $units = [
 // @call isSameUnit
 'year' => 'Y',
 // @call isSameUnit
 'week' => 'o-W',
 // @call isSameUnit
 'day' => 'Y-m-d',
 // @call isSameUnit
 'hour' => 'Y-m-d H',
 // @call isSameUnit
 'minute' => 'Y-m-d H:i',
 // @call isSameUnit
 'second' => 'Y-m-d H:i:s',
 // @call isSameUnit
 'micro' => 'Y-m-d H:i:s.u',
 // @call isSameUnit
 'microsecond' => 'Y-m-d H:i:s.u',
 ];
 if (isset($units[$unit])) {
 return $this->isSameAs($units[$unit], $date);
 }
 if (isset($this->{$unit})) {
 return $this->resolveCarbon($date)->{$unit} === $this->{$unit};
 }
 if ($this->localStrictModeEnabled ?? static::isStrictModeEnabled()) {
 throw new BadComparisonUnitException($unit);
 }
 return \false;
 }
 public function isCurrentUnit($unit)
 {
 return $this->{'isSame' . \ucfirst($unit)}();
 }
 public function isSameQuarter($date = null, $ofSameYear = \true)
 {
 $date = $this->resolveCarbon($date);
 return $this->quarter === $date->quarter && (!$ofSameYear || $this->isSameYear($date));
 }
 public function isSameMonth($date = null, $ofSameYear = \true)
 {
 return $this->isSameAs($ofSameYear ? 'Y-m' : 'm', $date);
 }
 public function isDayOfWeek($dayOfWeek)
 {
 if (\is_string($dayOfWeek) && \defined($constant = static::class . '::' . \strtoupper($dayOfWeek))) {
 $dayOfWeek = \constant($constant);
 }
 return $this->dayOfWeek === $dayOfWeek;
 }
 public function isBirthday($date = null)
 {
 return $this->isSameAs('md', $date);
 }
 public function isLastOfMonth()
 {
 return $this->day === $this->daysInMonth;
 }
 public function isStartOfDay($checkMicroseconds = \false)
 {
 return $checkMicroseconds ? $this->rawFormat('H:i:s.u') === '00:00:00.000000' : $this->rawFormat('H:i:s') === '00:00:00';
 }
 public function isEndOfDay($checkMicroseconds = \false)
 {
 return $checkMicroseconds ? $this->rawFormat('H:i:s.u') === '23:59:59.999999' : $this->rawFormat('H:i:s') === '23:59:59';
 }
 public function isMidnight()
 {
 return $this->isStartOfDay();
 }
 public function isMidday()
 {
 return $this->rawFormat('G:i:s') === static::$midDayAt . ':00:00';
 }
 public static function hasFormat($date, $format)
 {
 // createFromFormat() is known to handle edge cases silently.
 // E.g. "1975-5-1" (Y-n-j) will still be parsed correctly when "Y-m-d" is supplied as the format.
 // To ensure we're really testing against our desired format, perform an additional regex validation.
 return self::matchFormatPattern((string) $date, \preg_quote((string) $format, '/'), static::$regexFormats);
 }
 public static function hasFormatWithModifiers($date, $format) : bool
 {
 return self::matchFormatPattern((string) $date, (string) $format, \array_merge(static::$regexFormats, static::$regexFormatModifiers));
 }
 public static function canBeCreatedFromFormat($date, $format)
 {
 try {
 // Try to create a DateTime object. Throws an InvalidArgumentException if the provided time string
 // doesn't match the format in any way.
 if (!static::rawCreateFromFormat($format, $date)) {
 return \false;
 }
 } catch (InvalidArgumentException $e) {
 return \false;
 }
 return static::hasFormatWithModifiers($date, $format);
 }
 public function is(string $tester)
 {
 $tester = \trim($tester);
 if (\preg_match('/^\\d+$/', $tester)) {
 return $this->year === (int) $tester;
 }
 if (\preg_match('/^\\d{3,}-\\d{1,2}$/', $tester)) {
 return $this->isSameMonth(static::parse($tester));
 }
 if (\preg_match('/^\\d{1,2}-\\d{1,2}$/', $tester)) {
 return $this->isSameDay(static::parse($this->year . '-' . $tester));
 }
 $modifier = \preg_replace('/(\\d)h$/i', '$1:00', $tester);
 $median = static::parse('5555-06-15 12:30:30.555555')->modify($modifier);
 $current = $this->avoidMutation();
 $other = $this->avoidMutation()->modify($modifier);
 if ($current->eq($other)) {
 return \true;
 }
 if (\preg_match('/\\d:\\d{1,2}:\\d{1,2}$/', $tester)) {
 return $current->startOfSecond()->eq($other);
 }
 if (\preg_match('/\\d:\\d{1,2}$/', $tester)) {
 return $current->startOfMinute()->eq($other);
 }
 if (\preg_match('/\\d(h|am|pm)$/', $tester)) {
 return $current->startOfHour()->eq($other);
 }
 if (\preg_match('/^(january|february|march|april|may|june|july|august|september|october|november|december)\\s+\\d+$/i', $tester)) {
 return $current->startOfMonth()->eq($other->startOfMonth());
 }
 $units = ['month' => [1, 'year'], 'day' => [1, 'month'], 'hour' => [0, 'day'], 'minute' => [0, 'hour'], 'second' => [0, 'minute'], 'microsecond' => [0, 'second']];
 foreach ($units as $unit => [$minimum, $startUnit]) {
 if ($minimum === $median->{$unit}) {
 $current = $current->startOf($startUnit);
 break;
 }
 }
 return $current->eq($other);
 }
 private static function matchFormatPattern(string $date, string $format, array $replacements) : bool
 {
 // Preg quote, but remove escaped backslashes since we'll deal with escaped characters in the format string.
 $regex = \str_replace('\\\\', '\\', $format);
 // Replace not-escaped letters
 $regex = \preg_replace_callback('/(?<!\\\\)((?:\\\\{2})*)([' . \implode('', \array_keys($replacements)) . '])/', function ($match) use($replacements) {
 return $match[1] . \strtr($match[2], $replacements);
 }, $regex);
 // Replace escaped letters by the letter itself
 $regex = \preg_replace('/(?<!\\\\)((?:\\\\{2})*)\\\\(\\w)/', '$1$2', $regex);
 // Escape not escaped slashes
 $regex = \preg_replace('#(?<!\\\\)((?:\\\\{2})*)/#', '$1\\/', $regex);
 return (bool) @\preg_match('/^' . $regex . '$/', $date);
 }
 public function isStartOfTime() : bool
 {
 return $this->startOfTime ?? \false;
 }
 public function isEndOfTime() : bool
 {
 return $this->endOfTime ?? \false;
 }
 private function discourageNull($value) : void
 {
 if ($value === null) {
 @\trigger_error("Since 2.61.0, it's deprecated to compare a date to null, meaning of such comparison is ambiguous and will no longer be possible in 3.0.0, you should explicitly pass 'now' or make an other check to eliminate null values.", \E_USER_DEPRECATED);
 }
 }
 private function discourageBoolean($value) : void
 {
 if (\is_bool($value)) {
 @\trigger_error("Since 2.61.0, it's deprecated to compare a date to true or false, meaning of such comparison is ambiguous and will no longer be possible in 3.0.0, you should explicitly pass 'now' or make an other check to eliminate boolean values.", \E_USER_DEPRECATED);
 }
 }
}
