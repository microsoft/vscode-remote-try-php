<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\QueryCache;
use MailPoetVendor\Doctrine\ORM\Cache\Region;
interface Cache
{
 public const DEFAULT_QUERY_REGION_NAME = 'query_cache_region';
 public const DEFAULT_TIMESTAMP_REGION_NAME = 'timestamp_cache_region';
 public const MODE_GET = 1;
 public const MODE_PUT = 2;
 public const MODE_NORMAL = 3;
 public const MODE_REFRESH = 4;
 public function getEntityCacheRegion($className);
 public function getCollectionCacheRegion($className, $association);
 public function containsEntity($className, $identifier);
 public function evictEntity($className, $identifier);
 public function evictEntityRegion($className);
 public function evictEntityRegions();
 public function containsCollection($className, $association, $ownerIdentifier);
 public function evictCollection($className, $association, $ownerIdentifier);
 public function evictCollectionRegion($className, $association);
 public function evictCollectionRegions();
 public function containsQuery($regionName);
 public function evictQueryRegion($regionName = null);
 public function evictQueryRegions();
 public function getQueryCache($regionName = null);
}
