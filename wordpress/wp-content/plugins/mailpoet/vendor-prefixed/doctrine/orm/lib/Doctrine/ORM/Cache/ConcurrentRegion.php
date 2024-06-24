<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
interface ConcurrentRegion extends Region
{
 public function lock(CacheKey $key);
 public function unlock(CacheKey $key, Lock $lock);
}
