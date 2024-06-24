<?php
namespace Cron;
if (!defined('ABSPATH')) exit;
use DateTime;
interface FieldInterface
{
 public function isSatisfiedBy(DateTime $date, $value);
 public function increment(DateTime $date, $invert = false);
 public function validate($value);
}
