<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use function microtime;
class TimestampCacheEntry implements CacheEntry
{
 public $time;
 public function __construct($time = null)
 {
 $this->time = $time ? (float) $time : microtime(\true);
 }
 public static function __set_state(array $values)
 {
 return new self($values['time']);
 }
}
