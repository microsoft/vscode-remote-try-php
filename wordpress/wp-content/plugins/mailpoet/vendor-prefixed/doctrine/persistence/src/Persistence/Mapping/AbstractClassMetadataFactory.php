<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MailPoetVendor\Doctrine\Persistence\Proxy;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use ReflectionException;
use function array_combine;
use function array_keys;
use function array_map;
use function array_reverse;
use function array_unshift;
use function assert;
use function explode;
use function is_array;
use function str_replace;
use function strpos;
use function strrpos;
use function substr;
abstract class AbstractClassMetadataFactory implements ClassMetadataFactory
{
 protected $cacheSalt = '__CLASSMETADATA__';
 private $cacheDriver;
 private $cache;
 private $loadedMetadata = [];
 protected $initialized = \false;
 private $reflectionService = null;
 private $proxyClassNameResolver = null;
 public function setCacheDriver(?Cache $cacheDriver = null)
 {
 Deprecation::trigger('doctrine/persistence', 'https://github.com/doctrine/persistence/issues/184', '%s is deprecated. Use setCache() with a PSR-6 cache instead.', __METHOD__);
 $this->cacheDriver = $cacheDriver;
 if ($cacheDriver === null) {
 $this->cache = null;
 return;
 }
 $this->cache = CacheAdapter::wrap($cacheDriver);
 }
 public function getCacheDriver()
 {
 Deprecation::trigger('doctrine/persistence', 'https://github.com/doctrine/persistence/issues/184', '%s is deprecated. Use getCache() instead.', __METHOD__);
 return $this->cacheDriver;
 }
 public function setCache(CacheItemPoolInterface $cache) : void
 {
 $this->cache = $cache;
 $this->cacheDriver = DoctrineProvider::wrap($cache);
 }
 protected final function getCache() : ?CacheItemPoolInterface
 {
 return $this->cache;
 }
 public function getLoadedMetadata()
 {
 return $this->loadedMetadata;
 }
 public function getAllMetadata()
 {
 if (!$this->initialized) {
 $this->initialize();
 }
 $driver = $this->getDriver();
 $metadata = [];
 foreach ($driver->getAllClassNames() as $className) {
 $metadata[] = $this->getMetadataFor($className);
 }
 return $metadata;
 }
 public function setProxyClassNameResolver(ProxyClassNameResolver $resolver) : void
 {
 $this->proxyClassNameResolver = $resolver;
 }
 protected abstract function initialize();
 protected abstract function getFqcnFromAlias($namespaceAlias, $simpleClassName);
 protected abstract function getDriver();
 protected abstract function wakeupReflection(ClassMetadata $class, ReflectionService $reflService);
 protected abstract function initializeReflection(ClassMetadata $class, ReflectionService $reflService);
 protected abstract function isEntity(ClassMetadata $class);
 public function getMetadataFor($className)
 {
 if (isset($this->loadedMetadata[$className])) {
 return $this->loadedMetadata[$className];
 }
 // Check for namespace alias
 if (strpos($className, ':') !== \false) {
 Deprecation::trigger('doctrine/persistence', 'https://github.com/doctrine/persistence/issues/204', 'Short namespace aliases such as "%s" are deprecated, use ::class constant instead.', $className);
 [$namespaceAlias, $simpleClassName] = explode(':', $className, 2);
 $realClassName = $this->getFqcnFromAlias($namespaceAlias, $simpleClassName);
 } else {
 $realClassName = $this->getRealClass($className);
 }
 if (isset($this->loadedMetadata[$realClassName])) {
 // We do not have the alias name in the map, include it
 return $this->loadedMetadata[$className] = $this->loadedMetadata[$realClassName];
 }
 $loadingException = null;
 try {
 if ($this->cache) {
 $cached = $this->cache->getItem($this->getCacheKey($realClassName))->get();
 if ($cached instanceof ClassMetadata) {
 $this->loadedMetadata[$realClassName] = $cached;
 $this->wakeupReflection($cached, $this->getReflectionService());
 } else {
 $loadedMetadata = $this->loadMetadata($realClassName);
 $classNames = array_combine(array_map([$this, 'getCacheKey'], $loadedMetadata), $loadedMetadata);
 assert(is_array($classNames));
 foreach ($this->cache->getItems(array_keys($classNames)) as $item) {
 if (!isset($classNames[$item->getKey()])) {
 continue;
 }
 $item->set($this->loadedMetadata[$classNames[$item->getKey()]]);
 $this->cache->saveDeferred($item);
 }
 $this->cache->commit();
 }
 } else {
 $this->loadMetadata($realClassName);
 }
 } catch (MappingException $loadingException) {
 $fallbackMetadataResponse = $this->onNotFoundMetadata($realClassName);
 if (!$fallbackMetadataResponse) {
 throw $loadingException;
 }
 $this->loadedMetadata[$realClassName] = $fallbackMetadataResponse;
 }
 if ($className !== $realClassName) {
 // We do not have the alias name in the map, include it
 $this->loadedMetadata[$className] = $this->loadedMetadata[$realClassName];
 }
 return $this->loadedMetadata[$className];
 }
 public function hasMetadataFor($className)
 {
 return isset($this->loadedMetadata[$className]);
 }
 public function setMetadataFor($className, $class)
 {
 $this->loadedMetadata[$className] = $class;
 }
 protected function getParentClasses($name)
 {
 // Collect parent classes, ignoring transient (not-mapped) classes.
 $parentClasses = [];
 foreach (array_reverse($this->getReflectionService()->getParentClasses($name)) as $parentClass) {
 if ($this->getDriver()->isTransient($parentClass)) {
 continue;
 }
 $parentClasses[] = $parentClass;
 }
 return $parentClasses;
 }
 protected function loadMetadata($name)
 {
 if (!$this->initialized) {
 $this->initialize();
 }
 $loaded = [];
 $parentClasses = $this->getParentClasses($name);
 $parentClasses[] = $name;
 // Move down the hierarchy of parent classes, starting from the topmost class
 $parent = null;
 $rootEntityFound = \false;
 $visited = [];
 $reflService = $this->getReflectionService();
 foreach ($parentClasses as $className) {
 if (isset($this->loadedMetadata[$className])) {
 $parent = $this->loadedMetadata[$className];
 if ($this->isEntity($parent)) {
 $rootEntityFound = \true;
 array_unshift($visited, $className);
 }
 continue;
 }
 $class = $this->newClassMetadataInstance($className);
 $this->initializeReflection($class, $reflService);
 $this->doLoadMetadata($class, $parent, $rootEntityFound, $visited);
 $this->loadedMetadata[$className] = $class;
 $parent = $class;
 if ($this->isEntity($class)) {
 $rootEntityFound = \true;
 array_unshift($visited, $className);
 }
 $this->wakeupReflection($class, $reflService);
 $loaded[] = $className;
 }
 return $loaded;
 }
 protected function onNotFoundMetadata($className)
 {
 return null;
 }
 protected abstract function doLoadMetadata($class, $parent, $rootEntityFound, array $nonSuperclassParents);
 protected abstract function newClassMetadataInstance($className);
 public function isTransient($className)
 {
 if (!$this->initialized) {
 $this->initialize();
 }
 // Check for namespace alias
 if (strpos($className, ':') !== \false) {
 Deprecation::trigger('doctrine/persistence', 'https://github.com/doctrine/persistence/issues/204', 'Short namespace aliases such as "%s" are deprecated, use ::class constant instead.', $className);
 [$namespaceAlias, $simpleClassName] = explode(':', $className, 2);
 $className = $this->getFqcnFromAlias($namespaceAlias, $simpleClassName);
 }
 return $this->getDriver()->isTransient($className);
 }
 public function setReflectionService(ReflectionService $reflectionService)
 {
 $this->reflectionService = $reflectionService;
 }
 public function getReflectionService()
 {
 if ($this->reflectionService === null) {
 $this->reflectionService = new RuntimeReflectionService();
 }
 return $this->reflectionService;
 }
 protected function getCacheKey(string $realClassName) : string
 {
 return str_replace('\\', '__', $realClassName) . $this->cacheSalt;
 }
 private function getRealClass(string $class) : string
 {
 if ($this->proxyClassNameResolver === null) {
 $this->createDefaultProxyClassNameResolver();
 }
 assert($this->proxyClassNameResolver !== null);
 return $this->proxyClassNameResolver->resolveClassName($class);
 }
 private function createDefaultProxyClassNameResolver() : void
 {
 $this->proxyClassNameResolver = new class implements ProxyClassNameResolver
 {
 public function resolveClassName(string $className) : string
 {
 $pos = strrpos($className, '\\' . Proxy::MARKER . '\\');
 if ($pos === \false) {
 return $className;
 }
 return substr($className, $pos + Proxy::MARKER_LENGTH + 2);
 }
 };
 }
}
