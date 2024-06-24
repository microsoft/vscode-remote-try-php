<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
trait Week
{
 public function isoWeekYear($year = null, $dayOfWeek = null, $dayOfYear = null)
 {
 return $this->weekYear($year, $dayOfWeek ?? 1, $dayOfYear ?? 4);
 }
 public function weekYear($year = null, $dayOfWeek = null, $dayOfYear = null)
 {
 $dayOfWeek = $dayOfWeek ?? $this->getTranslationMessage('first_day_of_week') ?? 0;
 $dayOfYear = $dayOfYear ?? $this->getTranslationMessage('day_of_first_week_of_year') ?? 1;
 if ($year !== null) {
 $year = (int) \round($year);
 if ($this->weekYear(null, $dayOfWeek, $dayOfYear) === $year) {
 return $this->avoidMutation();
 }
 $week = $this->week(null, $dayOfWeek, $dayOfYear);
 $day = $this->dayOfWeek;
 $date = $this->year($year);
 switch ($date->weekYear(null, $dayOfWeek, $dayOfYear) - $year) {
 case 1:
 $date = $date->subWeeks(26);
 break;
 case -1:
 $date = $date->addWeeks(26);
 break;
 }
 $date = $date->addWeeks($week - $date->week(null, $dayOfWeek, $dayOfYear))->startOfWeek($dayOfWeek);
 if ($date->dayOfWeek === $day) {
 return $date;
 }
 return $date->next($day);
 }
 $year = $this->year;
 $day = $this->dayOfYear;
 $date = $this->avoidMutation()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
 if ($date->year === $year && $day < $date->dayOfYear) {
 return $year - 1;
 }
 $date = $this->avoidMutation()->addYear()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
 if ($date->year === $year && $day >= $date->dayOfYear) {
 return $year + 1;
 }
 return $year;
 }
 public function isoWeeksInYear($dayOfWeek = null, $dayOfYear = null)
 {
 return $this->weeksInYear($dayOfWeek ?? 1, $dayOfYear ?? 4);
 }
 public function weeksInYear($dayOfWeek = null, $dayOfYear = null)
 {
 $dayOfWeek = $dayOfWeek ?? $this->getTranslationMessage('first_day_of_week') ?? 0;
 $dayOfYear = $dayOfYear ?? $this->getTranslationMessage('day_of_first_week_of_year') ?? 1;
 $year = $this->year;
 $start = $this->avoidMutation()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
 $startDay = $start->dayOfYear;
 if ($start->year !== $year) {
 $startDay -= $start->daysInYear;
 }
 $end = $this->avoidMutation()->addYear()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
 $endDay = $end->dayOfYear;
 if ($end->year !== $year) {
 $endDay += $this->daysInYear;
 }
 return (int) \round(($endDay - $startDay) / 7);
 }
 public function week($week = null, $dayOfWeek = null, $dayOfYear = null)
 {
 $date = $this;
 $dayOfWeek = $dayOfWeek ?? $this->getTranslationMessage('first_day_of_week') ?? 0;
 $dayOfYear = $dayOfYear ?? $this->getTranslationMessage('day_of_first_week_of_year') ?? 1;
 if ($week !== null) {
 return $date->addWeeks(\round($week) - $this->week(null, $dayOfWeek, $dayOfYear));
 }
 $start = $date->avoidMutation()->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
 $end = $date->avoidMutation()->startOfWeek($dayOfWeek);
 if ($start > $end) {
 $start = $start->subWeeks(26)->dayOfYear($dayOfYear)->startOfWeek($dayOfWeek);
 }
 $week = (int) ($start->diffInDays($end) / 7 + 1);
 return $week > $end->weeksInYear($dayOfWeek, $dayOfYear) ? 1 : $week;
 }
 public function isoWeek($week = null, $dayOfWeek = null, $dayOfYear = null)
 {
 return $this->week($week, $dayOfWeek ?? 1, $dayOfYear ?? 4);
 }
}
