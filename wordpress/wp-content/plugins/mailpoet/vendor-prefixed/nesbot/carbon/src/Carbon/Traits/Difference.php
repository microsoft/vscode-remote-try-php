<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Carbon\CarbonInterface;
use MailPoetVendor\Carbon\CarbonInterval;
use MailPoetVendor\Carbon\CarbonPeriod;
use MailPoetVendor\Carbon\Translator;
use Closure;
use DateInterval;
use DateTimeInterface;
use ReturnTypeWillChange;
trait Difference
{
 protected static function fixNegativeMicroseconds(CarbonInterval $diff)
 {
 if ($diff->s !== 0 || $diff->i !== 0 || $diff->h !== 0 || $diff->d !== 0 || $diff->m !== 0 || $diff->y !== 0) {
 $diff->f = (\round($diff->f * 1000000) + 1000000) / 1000000;
 $diff->s--;
 if ($diff->s < 0) {
 $diff->s += 60;
 $diff->i--;
 if ($diff->i < 0) {
 $diff->i += 60;
 $diff->h--;
 if ($diff->h < 0) {
 $diff->h += 24;
 $diff->d--;
 if ($diff->d < 0) {
 $diff->d += 30;
 $diff->m--;
 if ($diff->m < 0) {
 $diff->m += 12;
 $diff->y--;
 }
 }
 }
 }
 }
 return;
 }
 $diff->f *= -1;
 $diff->invert();
 }
 protected static function fixDiffInterval(DateInterval $diff, $absolute, array $skip = [])
 {
 $diff = CarbonInterval::instance($diff, $skip);
 // Work-around for https://bugs.php.net/bug.php?id=77145
 // @codeCoverageIgnoreStart
 if ($diff->f > 0 && $diff->y === -1 && $diff->m === 11 && $diff->d >= 27 && $diff->h === 23 && $diff->i === 59 && $diff->s === 59) {
 $diff->y = 0;
 $diff->m = 0;
 $diff->d = 0;
 $diff->h = 0;
 $diff->i = 0;
 $diff->s = 0;
 $diff->f = (1000000 - \round($diff->f * 1000000)) / 1000000;
 $diff->invert();
 } elseif ($diff->f < 0) {
 static::fixNegativeMicroseconds($diff);
 }
 // @codeCoverageIgnoreEnd
 if ($absolute && $diff->invert) {
 $diff->invert();
 }
 return $diff;
 }
 #[\ReturnTypeWillChange]
 public function diff($date = null, $absolute = \false)
 {
 $other = $this->resolveCarbon($date);
 // Work-around for https://bugs.php.net/bug.php?id=81458
 // It was initially introduced for https://bugs.php.net/bug.php?id=80998
 // The very specific case of 80998 was fixed in PHP 8.1beta3, but it introduced 81458
 // So we still need to keep this for now
 // @codeCoverageIgnoreStart
 if (\version_compare(\PHP_VERSION, '8.1.0-dev', '>=') && $other->tz !== $this->tz) {
 $other = $other->avoidMutation()->tz($this->tz);
 }
 // @codeCoverageIgnoreEnd
 return parent::diff($other, (bool) $absolute);
 }
 public function diffAsCarbonInterval($date = null, $absolute = \true, array $skip = [])
 {
 return static::fixDiffInterval($this->diff($this->resolveCarbon($date), $absolute), $absolute, $skip);
 }
 public function diffInYears($date = null, $absolute = \true)
 {
 return (int) $this->diff($this->resolveCarbon($date), $absolute)->format('%r%y');
 }
 public function diffInQuarters($date = null, $absolute = \true)
 {
 return (int) ($this->diffInMonths($date, $absolute) / static::MONTHS_PER_QUARTER);
 }
 public function diffInMonths($date = null, $absolute = \true)
 {
 $date = $this->resolveCarbon($date)->avoidMutation()->tz($this->tz);
 [$yearStart, $monthStart, $dayStart] = \explode('-', $this->format('Y-m-dHisu'));
 [$yearEnd, $monthEnd, $dayEnd] = \explode('-', $date->format('Y-m-dHisu'));
 $diff = ((int) $yearEnd - (int) $yearStart) * static::MONTHS_PER_YEAR + (int) $monthEnd - (int) $monthStart;
 if ($diff > 0) {
 $diff -= $dayStart > $dayEnd ? 1 : 0;
 } elseif ($diff < 0) {
 $diff += $dayStart < $dayEnd ? 1 : 0;
 }
 return $absolute ? \abs($diff) : $diff;
 }
 public function diffInWeeks($date = null, $absolute = \true)
 {
 return (int) ($this->diffInDays($date, $absolute) / static::DAYS_PER_WEEK);
 }
 public function diffInDays($date = null, $absolute = \true)
 {
 return $this->getIntervalDayDiff($this->diff($this->resolveCarbon($date), $absolute));
 }
 public function diffInDaysFiltered(Closure $callback, $date = null, $absolute = \true)
 {
 return $this->diffFiltered(CarbonInterval::day(), $callback, $date, $absolute);
 }
 public function diffInHoursFiltered(Closure $callback, $date = null, $absolute = \true)
 {
 return $this->diffFiltered(CarbonInterval::hour(), $callback, $date, $absolute);
 }
 public function diffFiltered(CarbonInterval $ci, Closure $callback, $date = null, $absolute = \true)
 {
 $start = $this;
 $end = $this->resolveCarbon($date);
 $inverse = \false;
 if ($end < $start) {
 $start = $end;
 $end = $this;
 $inverse = \true;
 }
 $options = CarbonPeriod::EXCLUDE_END_DATE | ($this->isMutable() ? 0 : CarbonPeriod::IMMUTABLE);
 $diff = $ci->toPeriod($start, $end, $options)->filter($callback)->count();
 return $inverse && !$absolute ? -$diff : $diff;
 }
 public function diffInWeekdays($date = null, $absolute = \true)
 {
 return $this->diffInDaysFiltered(static function (CarbonInterface $date) {
 return $date->isWeekday();
 }, $this->resolveCarbon($date)->avoidMutation()->modify($this->format('H:i:s.u')), $absolute);
 }
 public function diffInWeekendDays($date = null, $absolute = \true)
 {
 return $this->diffInDaysFiltered(static function (CarbonInterface $date) {
 return $date->isWeekend();
 }, $this->resolveCarbon($date)->avoidMutation()->modify($this->format('H:i:s.u')), $absolute);
 }
 public function diffInHours($date = null, $absolute = \true)
 {
 return (int) ($this->diffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR);
 }
 public function diffInRealHours($date = null, $absolute = \true)
 {
 return (int) ($this->diffInRealSeconds($date, $absolute) / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR);
 }
 public function diffInMinutes($date = null, $absolute = \true)
 {
 return (int) ($this->diffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE);
 }
 public function diffInRealMinutes($date = null, $absolute = \true)
 {
 return (int) ($this->diffInRealSeconds($date, $absolute) / static::SECONDS_PER_MINUTE);
 }
 public function diffInSeconds($date = null, $absolute = \true)
 {
 $diff = $this->diff($date);
 if ($diff->days === 0) {
 $diff = static::fixDiffInterval($diff, $absolute);
 }
 $value = ((($diff->m || $diff->y ? $diff->days : $diff->d) * static::HOURS_PER_DAY + $diff->h) * static::MINUTES_PER_HOUR + $diff->i) * static::SECONDS_PER_MINUTE + $diff->s;
 return $absolute || !$diff->invert ? $value : -$value;
 }
 public function diffInMicroseconds($date = null, $absolute = \true)
 {
 $diff = $this->diff($date);
 $value = (int) \round((((($diff->m || $diff->y ? $diff->days : $diff->d) * static::HOURS_PER_DAY + $diff->h) * static::MINUTES_PER_HOUR + $diff->i) * static::SECONDS_PER_MINUTE + ($diff->f + $diff->s)) * static::MICROSECONDS_PER_SECOND);
 return $absolute || !$diff->invert ? $value : -$value;
 }
 public function diffInMilliseconds($date = null, $absolute = \true)
 {
 return (int) ($this->diffInMicroseconds($date, $absolute) / static::MICROSECONDS_PER_MILLISECOND);
 }
 public function diffInRealSeconds($date = null, $absolute = \true)
 {
 $date = $this->resolveCarbon($date);
 $value = $date->getTimestamp() - $this->getTimestamp();
 return $absolute ? \abs($value) : $value;
 }
 public function diffInRealMicroseconds($date = null, $absolute = \true)
 {
 $date = $this->resolveCarbon($date);
 $value = ($date->timestamp - $this->timestamp) * static::MICROSECONDS_PER_SECOND + $date->micro - $this->micro;
 return $absolute ? \abs($value) : $value;
 }
 public function diffInRealMilliseconds($date = null, $absolute = \true)
 {
 return (int) ($this->diffInRealMicroseconds($date, $absolute) / static::MICROSECONDS_PER_MILLISECOND);
 }
 public function floatDiffInSeconds($date = null, $absolute = \true)
 {
 return (float) ($this->diffInMicroseconds($date, $absolute) / static::MICROSECONDS_PER_SECOND);
 }
 public function floatDiffInMinutes($date = null, $absolute = \true)
 {
 return $this->floatDiffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE;
 }
 public function floatDiffInHours($date = null, $absolute = \true)
 {
 return $this->floatDiffInMinutes($date, $absolute) / static::MINUTES_PER_HOUR;
 }
 public function floatDiffInDays($date = null, $absolute = \true)
 {
 $hoursDiff = $this->floatDiffInHours($date, $absolute);
 $interval = $this->diff($date, $absolute);
 if ($interval->y === 0 && $interval->m === 0 && $interval->d === 0) {
 return $hoursDiff / static::HOURS_PER_DAY;
 }
 $daysDiff = $this->getIntervalDayDiff($interval);
 return $daysDiff + \fmod($hoursDiff, static::HOURS_PER_DAY) / static::HOURS_PER_DAY;
 }
 public function floatDiffInWeeks($date = null, $absolute = \true)
 {
 return $this->floatDiffInDays($date, $absolute) / static::DAYS_PER_WEEK;
 }
 public function floatDiffInMonths($date = null, $absolute = \true)
 {
 $start = $this;
 $end = $this->resolveCarbon($date);
 $ascending = $start <= $end;
 $sign = $absolute || $ascending ? 1 : -1;
 if (!$ascending) {
 [$start, $end] = [$end, $start];
 }
 $monthsDiff = $start->diffInMonths($end);
 $floorEnd = $start->avoidMutation()->addMonths($monthsDiff);
 if ($floorEnd >= $end) {
 return $sign * $monthsDiff;
 }
 $startOfMonthAfterFloorEnd = $floorEnd->avoidMutation()->addMonth()->startOfMonth();
 if ($startOfMonthAfterFloorEnd > $end) {
 return $sign * ($monthsDiff + $floorEnd->floatDiffInDays($end) / $floorEnd->daysInMonth);
 }
 return $sign * ($monthsDiff + $floorEnd->floatDiffInDays($startOfMonthAfterFloorEnd) / $floorEnd->daysInMonth + $startOfMonthAfterFloorEnd->floatDiffInDays($end) / $end->daysInMonth);
 }
 public function floatDiffInYears($date = null, $absolute = \true)
 {
 $start = $this;
 $end = $this->resolveCarbon($date);
 $ascending = $start <= $end;
 $sign = $absolute || $ascending ? 1 : -1;
 if (!$ascending) {
 [$start, $end] = [$end, $start];
 }
 $yearsDiff = $start->diffInYears($end);
 $floorEnd = $start->avoidMutation()->addYears($yearsDiff);
 if ($floorEnd >= $end) {
 return $sign * $yearsDiff;
 }
 $startOfYearAfterFloorEnd = $floorEnd->avoidMutation()->addYear()->startOfYear();
 if ($startOfYearAfterFloorEnd > $end) {
 return $sign * ($yearsDiff + $floorEnd->floatDiffInDays($end) / $floorEnd->daysInYear);
 }
 return $sign * ($yearsDiff + $floorEnd->floatDiffInDays($startOfYearAfterFloorEnd) / $floorEnd->daysInYear + $startOfYearAfterFloorEnd->floatDiffInDays($end) / $end->daysInYear);
 }
 public function floatDiffInRealSeconds($date = null, $absolute = \true)
 {
 return $this->diffInRealMicroseconds($date, $absolute) / static::MICROSECONDS_PER_SECOND;
 }
 public function floatDiffInRealMinutes($date = null, $absolute = \true)
 {
 return $this->floatDiffInRealSeconds($date, $absolute) / static::SECONDS_PER_MINUTE;
 }
 public function floatDiffInRealHours($date = null, $absolute = \true)
 {
 return $this->floatDiffInRealMinutes($date, $absolute) / static::MINUTES_PER_HOUR;
 }
 public function floatDiffInRealDays($date = null, $absolute = \true)
 {
 $date = $this->resolveUTC($date);
 $utc = $this->avoidMutation()->utc();
 $hoursDiff = $utc->floatDiffInRealHours($date, $absolute);
 return ($hoursDiff < 0 ? -1 : 1) * $utc->diffInDays($date) + \fmod($hoursDiff, static::HOURS_PER_DAY) / static::HOURS_PER_DAY;
 }
 public function floatDiffInRealWeeks($date = null, $absolute = \true)
 {
 return $this->floatDiffInRealDays($date, $absolute) / static::DAYS_PER_WEEK;
 }
 public function floatDiffInRealMonths($date = null, $absolute = \true)
 {
 $start = $this;
 $end = $this->resolveCarbon($date);
 $ascending = $start <= $end;
 $sign = $absolute || $ascending ? 1 : -1;
 if (!$ascending) {
 [$start, $end] = [$end, $start];
 }
 $monthsDiff = $start->diffInMonths($end);
 $floorEnd = $start->avoidMutation()->addMonths($monthsDiff);
 if ($floorEnd >= $end) {
 return $sign * $monthsDiff;
 }
 $startOfMonthAfterFloorEnd = $floorEnd->avoidMutation()->addMonth()->startOfMonth();
 if ($startOfMonthAfterFloorEnd > $end) {
 return $sign * ($monthsDiff + $floorEnd->floatDiffInRealDays($end) / $floorEnd->daysInMonth);
 }
 return $sign * ($monthsDiff + $floorEnd->floatDiffInRealDays($startOfMonthAfterFloorEnd) / $floorEnd->daysInMonth + $startOfMonthAfterFloorEnd->floatDiffInRealDays($end) / $end->daysInMonth);
 }
 public function floatDiffInRealYears($date = null, $absolute = \true)
 {
 $start = $this;
 $end = $this->resolveCarbon($date);
 $ascending = $start <= $end;
 $sign = $absolute || $ascending ? 1 : -1;
 if (!$ascending) {
 [$start, $end] = [$end, $start];
 }
 $yearsDiff = $start->diffInYears($end);
 $floorEnd = $start->avoidMutation()->addYears($yearsDiff);
 if ($floorEnd >= $end) {
 return $sign * $yearsDiff;
 }
 $startOfYearAfterFloorEnd = $floorEnd->avoidMutation()->addYear()->startOfYear();
 if ($startOfYearAfterFloorEnd > $end) {
 return $sign * ($yearsDiff + $floorEnd->floatDiffInRealDays($end) / $floorEnd->daysInYear);
 }
 return $sign * ($yearsDiff + $floorEnd->floatDiffInRealDays($startOfYearAfterFloorEnd) / $floorEnd->daysInYear + $startOfYearAfterFloorEnd->floatDiffInRealDays($end) / $end->daysInYear);
 }
 public function secondsSinceMidnight()
 {
 return $this->diffInSeconds($this->avoidMutation()->startOfDay());
 }
 public function secondsUntilEndOfDay()
 {
 return $this->diffInSeconds($this->avoidMutation()->endOfDay());
 }
 public function diffForHumans($other = null, $syntax = null, $short = \false, $parts = 1, $options = null)
 {
 if (\is_array($other)) {
 $other['syntax'] = \array_key_exists('syntax', $other) ? $other['syntax'] : $syntax;
 $syntax = $other;
 $other = $syntax['other'] ?? null;
 }
 $intSyntax =& $syntax;
 if (\is_array($syntax)) {
 $syntax['syntax'] = $syntax['syntax'] ?? null;
 $intSyntax =& $syntax['syntax'];
 }
 $intSyntax = (int) ($intSyntax ?? static::DIFF_RELATIVE_AUTO);
 $intSyntax = $intSyntax === static::DIFF_RELATIVE_AUTO && $other === null ? static::DIFF_RELATIVE_TO_NOW : $intSyntax;
 $parts = \min(7, \max(1, (int) $parts));
 $skip = \is_array($syntax) ? $syntax['skip'] ?? [] : [];
 return $this->diffAsCarbonInterval($other, \false, (array) $skip)->setLocalTranslator($this->getLocalTranslator())->forHumans($syntax, (bool) $short, $parts, $options ?? $this->localHumanDiffOptions ?? static::getHumanDiffOptions());
 }
 public function from($other = null, $syntax = null, $short = \false, $parts = 1, $options = null)
 {
 return $this->diffForHumans($other, $syntax, $short, $parts, $options);
 }
 public function since($other = null, $syntax = null, $short = \false, $parts = 1, $options = null)
 {
 return $this->diffForHumans($other, $syntax, $short, $parts, $options);
 }
 public function to($other = null, $syntax = null, $short = \false, $parts = 1, $options = null)
 {
 if (!$syntax && !$other) {
 $syntax = CarbonInterface::DIFF_RELATIVE_TO_NOW;
 }
 return $this->resolveCarbon($other)->diffForHumans($this, $syntax, $short, $parts, $options);
 }
 public function until($other = null, $syntax = null, $short = \false, $parts = 1, $options = null)
 {
 return $this->to($other, $syntax, $short, $parts, $options);
 }
 public function fromNow($syntax = null, $short = \false, $parts = 1, $options = null)
 {
 $other = null;
 if ($syntax instanceof DateTimeInterface) {
 [$other, $syntax, $short, $parts, $options] = \array_pad(\func_get_args(), 5, null);
 }
 return $this->from($other, $syntax, $short, $parts, $options);
 }
 public function toNow($syntax = null, $short = \false, $parts = 1, $options = null)
 {
 return $this->to(null, $syntax, $short, $parts, $options);
 }
 public function ago($syntax = null, $short = \false, $parts = 1, $options = null)
 {
 $other = null;
 if ($syntax instanceof DateTimeInterface) {
 [$other, $syntax, $short, $parts, $options] = \array_pad(\func_get_args(), 5, null);
 }
 return $this->from($other, $syntax, $short, $parts, $options);
 }
 public function timespan($other = null, $timezone = null)
 {
 if (!$other instanceof DateTimeInterface) {
 $other = static::parse($other, $timezone);
 }
 return $this->diffForHumans($other, ['join' => ', ', 'syntax' => CarbonInterface::DIFF_ABSOLUTE, 'options' => CarbonInterface::NO_ZERO_DIFF, 'parts' => -1]);
 }
 public function calendar($referenceTime = null, array $formats = [])
 {
 $current = $this->avoidMutation()->startOfDay();
 $other = $this->resolveCarbon($referenceTime)->avoidMutation()->setTimezone($this->getTimezone())->startOfDay();
 $diff = $other->diffInDays($current, \false);
 $format = $diff < -6 ? 'sameElse' : ($diff < -1 ? 'lastWeek' : ($diff < 0 ? 'lastDay' : ($diff < 1 ? 'sameDay' : ($diff < 2 ? 'nextDay' : ($diff < 7 ? 'nextWeek' : 'sameElse')))));
 $format = \array_merge($this->getCalendarFormats(), $formats)[$format];
 if ($format instanceof Closure) {
 $format = $format($current, $other) ?? '';
 }
 return $this->isoFormat((string) $format);
 }
 private function getIntervalDayDiff(DateInterval $interval) : int
 {
 $daysDiff = (int) $interval->format('%a');
 $sign = $interval->format('%r') === '-' ? -1 : 1;
 if (\is_int($interval->days) && $interval->y === 0 && $interval->m === 0 && \version_compare(\PHP_VERSION, '8.1.0-dev', '<') && \abs($interval->d - $daysDiff) === 1) {
 $daysDiff = \abs($interval->d);
 // @codeCoverageIgnore
 }
 return $daysDiff * $sign;
 }
}
