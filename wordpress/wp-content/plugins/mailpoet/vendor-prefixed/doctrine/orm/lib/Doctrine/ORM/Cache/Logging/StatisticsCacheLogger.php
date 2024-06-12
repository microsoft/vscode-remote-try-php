<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\QueryCacheKey;
use function array_sum;
class StatisticsCacheLogger implements CacheLogger
{
 private $cacheMissCountMap = [];
 private $cacheHitCountMap = [];
 private $cachePutCountMap = [];
 public function collectionCacheMiss($regionName, CollectionCacheKey $key)
 {
 $this->cacheMissCountMap[$regionName] = isset($this->cacheMissCountMap[$regionName]) ? $this->cacheMissCountMap[$regionName] + 1 : 1;
 }
 public function collectionCacheHit($regionName, CollectionCacheKey $key)
 {
 $this->cacheHitCountMap[$regionName] = isset($this->cacheHitCountMap[$regionName]) ? $this->cacheHitCountMap[$regionName] + 1 : 1;
 }
 public function collectionCachePut($regionName, CollectionCacheKey $key)
 {
 $this->cachePutCountMap[$regionName] = isset($this->cachePutCountMap[$regionName]) ? $this->cachePutCountMap[$regionName] + 1 : 1;
 }
 public function entityCacheMiss($regionName, EntityCacheKey $key)
 {
 $this->cacheMissCountMap[$regionName] = isset($this->cacheMissCountMap[$regionName]) ? $this->cacheMissCountMap[$regionName] + 1 : 1;
 }
 public function entityCacheHit($regionName, EntityCacheKey $key)
 {
 $this->cacheHitCountMap[$regionName] = isset($this->cacheHitCountMap[$regionName]) ? $this->cacheHitCountMap[$regionName] + 1 : 1;
 }
 public function entityCachePut($regionName, EntityCacheKey $key)
 {
 $this->cachePutCountMap[$regionName] = isset($this->cachePutCountMap[$regionName]) ? $this->cachePutCountMap[$regionName] + 1 : 1;
 }
 public function queryCacheHit($regionName, QueryCacheKey $key)
 {
 $this->cacheHitCountMap[$regionName] = isset($this->cacheHitCountMap[$regionName]) ? $this->cacheHitCountMap[$regionName] + 1 : 1;
 }
 public function queryCacheMiss($regionName, QueryCacheKey $key)
 {
 $this->cacheMissCountMap[$regionName] = isset($this->cacheMissCountMap[$regionName]) ? $this->cacheMissCountMap[$regionName] + 1 : 1;
 }
 public function queryCachePut($regionName, QueryCacheKey $key)
 {
 $this->cachePutCountMap[$regionName] = isset($this->cachePutCountMap[$regionName]) ? $this->cachePutCountMap[$regionName] + 1 : 1;
 }
 public function getRegionHitCount($regionName)
 {
 return $this->cacheHitCountMap[$regionName] ?? 0;
 }
 public function getRegionMissCount($regionName)
 {
 return $this->cacheMissCountMap[$regionName] ?? 0;
 }
 public function getRegionPutCount($regionName)
 {
 return $this->cachePutCountMap[$regionName] ?? 0;
 }
 public function getRegionsMiss()
 {
 return $this->cacheMissCountMap;
 }
 public function getRegionsHit()
 {
 return $this->cacheHitCountMap;
 }
 public function getRegionsPut()
 {
 return $this->cachePutCountMap;
 }
 public function clearRegionStats($regionName)
 {
 $this->cachePutCountMap[$regionName] = 0;
 $this->cacheHitCountMap[$regionName] = 0;
 $this->cacheMissCountMap[$regionName] = 0;
 }
 public function clearStats()
 {
 $this->cachePutCountMap = [];
 $this->cacheHitCountMap = [];
 $this->cacheMissCountMap = [];
 }
 public function getPutCount()
 {
 return array_sum($this->cachePutCountMap);
 }
 public function getHitCount()
 {
 return array_sum($this->cacheHitCountMap);
 }
 public function getMissCount()
 {
 return array_sum($this->cacheMissCountMap);
 }
}
