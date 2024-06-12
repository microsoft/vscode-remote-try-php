<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Region;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\CacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\TimestampCacheEntry;
use MailPoetVendor\Doctrine\ORM\Cache\TimestampRegion;
class UpdateTimestampCache extends DefaultRegion implements TimestampRegion
{
 public function update(CacheKey $key)
 {
 $this->put($key, new TimestampCacheEntry());
 }
}
