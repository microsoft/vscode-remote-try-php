<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use Closure;
trait ToStringFormat
{
 protected static $toStringFormat;
 public static function resetToStringFormat()
 {
 static::setToStringFormat(null);
 }
 public static function setToStringFormat($format)
 {
 static::$toStringFormat = $format;
 }
}
