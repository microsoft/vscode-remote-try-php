<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Region;
if (!defined('ABSPATH')) exit;
use Closure;
use MailPoetVendor\Doctrine\Common\Cache\Cache as LegacyCache;
use MailPoetVendor\Doctrine\Common\Cache\CacheProvider;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Cache\CacheEntry;
use MailPoetVendor\Doctrine\ORM\Cache\CacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheEntry;
use MailPoetVendor\Doctrine\ORM\Cache\Lock;
use MailPoetVendor\Doctrine\ORM\Cache\Region;
use MailPoetVendor\Psr\Cache\CacheItemInterface;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use Traversable;
use TypeError;
use function array_map;
use function get_debug_type;
use function iterator_to_array;
use function sprintf;
use function strtr;
class DefaultRegion implements Region
{
 public const REGION_KEY_SEPARATOR = '_';
 private const REGION_PREFIX = 'DC2_REGION_';
 protected $cache;
 protected $name;
 protected $lifetime = 0;
 private $cacheItemPool;
 public function __construct(string $name, $cacheItemPool, int $lifetime = 0)
 {
 if ($cacheItemPool instanceof LegacyCache) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9322', 'Passing an instance of %s to %s is deprecated, pass a %s instead.', get_debug_type($cacheItemPool), __METHOD__, CacheItemPoolInterface::class);
 $this->cache = $cacheItemPool;
 $this->cacheItemPool = CacheAdapter::wrap($cacheItemPool);
 } elseif (!$cacheItemPool instanceof CacheItemPoolInterface) {
 throw new TypeError(sprintf('%s: Parameter #2 is expected to be an instance of %s, got %s.', __METHOD__, CacheItemPoolInterface::class, get_debug_type($cacheItemPool)));
 } else {
 $this->cache = DoctrineProvider::wrap($cacheItemPool);
 $this->cacheItemPool = $cacheItemPool;
 }
 $this->name = $name;
 $this->lifetime = $lifetime;
 }
 public function getName()
 {
 return $this->name;
 }
 public function getCache()
 {
 return $this->cache;
 }
 public function contains(CacheKey $key)
 {
 return $this->cacheItemPool->hasItem($this->getCacheEntryKey($key));
 }
 public function get(CacheKey $key)
 {
 $item = $this->cacheItemPool->getItem($this->getCacheEntryKey($key));
 $entry = $item->isHit() ? $item->get() : null;
 if (!$entry instanceof CacheEntry) {
 return null;
 }
 return $entry;
 }
 public function getMultiple(CollectionCacheEntry $collection)
 {
 $keys = array_map(Closure::fromCallable([$this, 'getCacheEntryKey']), $collection->identifiers);
 $items = $this->cacheItemPool->getItems($keys);
 if ($items instanceof Traversable) {
 $items = iterator_to_array($items);
 }
 $result = [];
 foreach ($keys as $arrayKey => $cacheKey) {
 if (!isset($items[$cacheKey]) || !$items[$cacheKey]->isHit()) {
 return null;
 }
 $entry = $items[$cacheKey]->get();
 if (!$entry instanceof CacheEntry) {
 return null;
 }
 $result[$arrayKey] = $entry;
 }
 return $result;
 }
 public function put(CacheKey $key, CacheEntry $entry, ?Lock $lock = null)
 {
 $item = $this->cacheItemPool->getItem($this->getCacheEntryKey($key))->set($entry);
 if ($this->lifetime > 0) {
 $item->expiresAfter($this->lifetime);
 }
 return $this->cacheItemPool->save($item);
 }
 public function evict(CacheKey $key)
 {
 return $this->cacheItemPool->deleteItem($this->getCacheEntryKey($key));
 }
 public function evictAll()
 {
 return $this->cacheItemPool->clear(self::REGION_PREFIX . $this->name);
 }
 protected function getCacheEntryKey(CacheKey $key)
 {
 return self::REGION_PREFIX . $this->name . self::REGION_KEY_SEPARATOR . strtr($key->hash, '{}()/\\@:', '________');
 }
}
