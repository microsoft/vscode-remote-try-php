<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use function microtime;
class QueryCacheEntry implements CacheEntry
{
 public $result;
 public $time;
 public function __construct($result, $time = null)
 {
 $this->result = $result;
 $this->time = $time ?: microtime(\true);
 }
 public static function __set_state(array $values)
 {
 return new self($values['result'], $values['time']);
 }
}
