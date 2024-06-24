<?php
namespace MailPoetVendor\Doctrine\DBAL\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use TypeError;
use function get_class;
use function hash;
use function serialize;
use function sha1;
use function sprintf;
class QueryCacheProfile
{
 private ?CacheItemPoolInterface $resultCache = null;
 private $lifetime;
 private $cacheKey;
 public function __construct($lifetime = 0, $cacheKey = null, ?object $resultCache = null)
 {
 $this->lifetime = $lifetime;
 $this->cacheKey = $cacheKey;
 if ($resultCache instanceof CacheItemPoolInterface) {
 $this->resultCache = $resultCache;
 } elseif ($resultCache instanceof Cache) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4620', 'Passing an instance of %s to %s as $resultCache is deprecated. Pass an instance of %s instead.', Cache::class, __METHOD__, CacheItemPoolInterface::class);
 $this->resultCache = CacheAdapter::wrap($resultCache);
 } elseif ($resultCache !== null) {
 throw new TypeError(sprintf('$resultCache: Expected either null or an instance of %s or %s, got %s.', CacheItemPoolInterface::class, Cache::class, get_class($resultCache)));
 }
 }
 public function getResultCache() : ?CacheItemPoolInterface
 {
 return $this->resultCache;
 }
 public function getResultCacheDriver()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4620', '%s is deprecated, call getResultCache() instead.', __METHOD__);
 return $this->resultCache !== null ? DoctrineProvider::wrap($this->resultCache) : null;
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
 if (isset($connectionParams['password'])) {
 unset($connectionParams['password']);
 }
 $realCacheKey = 'query=' . $sql . '&params=' . serialize($params) . '&types=' . serialize($types) . '&connectionParams=' . hash('sha256', serialize($connectionParams));
 // should the key be automatically generated using the inputs or is the cache key set?
 $cacheKey = $this->cacheKey ?? sha1($realCacheKey);
 return [$cacheKey, $realCacheKey];
 }
 public function setResultCache(CacheItemPoolInterface $cache) : QueryCacheProfile
 {
 return new QueryCacheProfile($this->lifetime, $this->cacheKey, $cache);
 }
 public function setResultCacheDriver(Cache $cache)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4620', '%s is deprecated, call setResultCache() instead.', __METHOD__);
 return new QueryCacheProfile($this->lifetime, $this->cacheKey, CacheAdapter::wrap($cache));
 }
 public function setCacheKey($cacheKey)
 {
 return new QueryCacheProfile($this->lifetime, $cacheKey, $this->resultCache);
 }
 public function setLifetime($lifetime)
 {
 return new QueryCacheProfile($lifetime, $this->cacheKey, $this->resultCache);
 }
}
