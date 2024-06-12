<?php
namespace MailPoetVendor\Doctrine\DBAL\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use function hash;
use function serialize;
use function sha1;
class QueryCacheProfile
{
 private $resultCacheDriver;
 private $lifetime = 0;
 private $cacheKey;
 public function __construct($lifetime = 0, $cacheKey = null, ?Cache $resultCache = null)
 {
 $this->lifetime = $lifetime;
 $this->cacheKey = $cacheKey;
 $this->resultCacheDriver = $resultCache;
 }
 public function getResultCacheDriver()
 {
 return $this->resultCacheDriver;
 }
 public function getLifetime()
 {
 return $this->lifetime;
 }
 public function getCacheKey()
 {
 if ($this->cacheKey === null) {
 throw CacheException::noCacheKey();
 }
 return $this->cacheKey;
 }
 public function generateCacheKeys($sql, $params, $types, array $connectionParams = [])
 {
 $realCacheKey = 'query=' . $sql . '&params=' . serialize($params) . '&types=' . serialize($types) . '&connectionParams=' . hash('sha256', serialize($connectionParams));
 // should the key be automatically generated using the inputs or is the cache key set?
 if ($this->cacheKey === null) {
 $cacheKey = sha1($realCacheKey);
 } else {
 $cacheKey = $this->cacheKey;
 }
 return [$cacheKey, $realCacheKey];
 }
 public function setResultCacheDriver(Cache $cache)
 {
 return new QueryCacheProfile($this->lifetime, $this->cacheKey, $cache);
 }
 public function setCacheKey($cacheKey)
 {
 return new QueryCacheProfile($this->lifetime, $cacheKey, $this->resultCacheDriver);
 }
 public function setLifetime($lifetime)
 {
 return new QueryCacheProfile($lifetime, $this->cacheKey, $this->resultCacheDriver);
 }
}
