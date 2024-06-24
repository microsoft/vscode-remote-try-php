<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\QueryCacheKey;
interface CacheLogger
{
 public function entityCachePut($regionName, EntityCacheKey $key);
 public function entityCacheHit($regionName, EntityCacheKey $key);
 public function entityCacheMiss($regionName, EntityCacheKey $key);
 public function collectionCachePut($regionName, CollectionCacheKey $key);
 public function collectionCacheHit($regionName, CollectionCacheKey $key);
 public function collectionCacheMiss($regionName, CollectionCacheKey $key);
 public function queryCachePut($regionName, QueryCacheKey $key);
 public function queryCacheHit($regionName, QueryCacheKey $key);
 public function queryCacheMiss($regionName, QueryCacheKey $key);
}
