<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Carbon\CarbonInterface;
use Closure;
use DateTimeImmutable;
use DateTimeInterface;
trait IntervalStep
{
 protected $step;
 public function getStep() : ?Closure
 {
 return $this->step;
 }
 public function setStep(?Closure $step) : void
 {
 $this->step = $step;
 }
 public function convertDate(DateTimeInterface $dateTime, bool $negated = \false) : CarbonInterface
 {
 $carbonDate = $dateTime instanceof CarbonInterface ? $dateTime : $this->resolveCarbon($dateTime);
 if ($this->step) {
 return $carbonDate->setDateTimeFrom(($this->step)($carbonDate->avoidMutation(), $negated));
 }
 if ($negated) {
 return $carbonDate->rawSub($this);
 }
 return $carbonDate->rawAdd($this);
 }
 private function resolveCarbon(DateTimeInterface $dateTime)
 {
 if ($dateTime instanceof DateTimeImmutable) {
 return CarbonImmutable::instance($dateTime);
 }
 return Carbon::instance($dateTime);
 }
}
