<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
class CarbonPeriodImmutable extends CarbonPeriod
{
 protected const DEFAULT_DATE_CLASS = CarbonImmutable::class;
 protected $dateClass = CarbonImmutable::class;
 protected function copyIfImmutable()
 {
 return $this->constructed ? clone $this : $this;
 }
}
