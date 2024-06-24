<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\CacheException;
interface Region extends MultiGetRegion
{
 public function getName();
 public function contains(CacheKey $key);
 public function get(CacheKey $key);
 public function put(CacheKey $key, CacheEntry $entry, ?Lock $lock = null);
 public function evict(CacheKey $key);
 public function evictAll();
}
