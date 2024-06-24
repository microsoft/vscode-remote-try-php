<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
final class DateIntervalUnit
{
 public const SECOND = 'SECOND';
 public const MINUTE = 'MINUTE';
 public const HOUR = 'HOUR';
 public const DAY = 'DAY';
 public const WEEK = 'WEEK';
 public const MONTH = 'MONTH';
 public const QUARTER = 'QUARTER';
 public const YEAR = 'YEAR';
 private function __construct()
 {
 }
}
