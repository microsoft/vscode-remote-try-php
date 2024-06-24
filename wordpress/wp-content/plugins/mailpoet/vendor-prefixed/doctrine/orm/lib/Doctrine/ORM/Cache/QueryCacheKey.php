<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache;
class QueryCacheKey extends CacheKey
{
 public $lifetime;
 public $cacheMode;
 public $timestampKey;
 public function __construct(string $cacheId, int $lifetime = 0, int $cacheMode = Cache::MODE_NORMAL, ?TimestampCacheKey $timestampKey = null)
 {
 $this->lifetime = $lifetime;
 $this->cacheMode = $cacheMode;
 $this->timestampKey = $timestampKey;
 parent::__construct($cacheId);
 }
}
