<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
use function array_combine;
use function array_key_exists;
use function array_map;
use function sprintf;
abstract class CacheProvider implements Cache, FlushableCache, ClearableCache, MultiOperationCache
{
 public const DOCTRINE_NAMESPACE_CACHEKEY = 'DoctrineNamespaceCacheKey[%s]';
 private $namespace = '';
 private $namespaceVersion;
 public function setNamespace($namespace)
 {
 $this->namespace = (string) $namespace;
 $this->namespaceVersion = null;
 }
 public function getNamespace()
 {
 return $this->namespace;
 }
 public function fetch($id)
 {
 return $this->doFetch($this->getNamespacedId($id));
 }
 public function fetchMultiple(array $keys)
 {
 if (empty($keys)) {
 return [];
 }
 // note: the array_combine() is in place to keep an association between our $keys and the $namespacedKeys
 $namespacedKeys = array_combine($keys, array_map([$this, 'getNamespacedId'], $keys));
 $items = $this->doFetchMultiple($namespacedKeys);
 $foundItems = [];
 // no internal array function supports this sort of mapping: needs to be iterative
 // this filters and combines keys in one pass
 foreach ($namespacedKeys as $requestedKey => $namespacedKey) {
 if (!isset($items[$namespacedKey]) && !array_key_exists($namespacedKey, $items)) {
 continue;
 }
 $foundItems[$requestedKey] = $items[$namespacedKey];
 }
 return $foundItems;
 }
 public function saveMultiple(array $keysAndValues, $lifetime = 0)
 {
 $namespacedKeysAndValues = [];
 foreach ($keysAndValues as $key => $value) {
 $namespacedKeysAndValues[$this->getNamespacedId($key)] = $value;
 }
 return $this->doSaveMultiple($namespacedKeysAndValues, $lifetime);
 }
 public function contains($id)
 {
 return $this->doContains($this->getNamespacedId($id));
 }
 public function save($id, $data, $lifeTime = 0)
 {
 return $this->doSave($this->getNamespacedId($id), $data, $lifeTime);
 }
 public function deleteMultiple(array $keys)
 {
 return $this->doDeleteMultiple(array_map([$this, 'getNamespacedId'], $keys));
 }
 public function delete($id)
 {
 return $this->doDelete($this->getNamespacedId($id));
 }
 public function getStats()
 {
 return $this->doGetStats();
 }
 public function flushAll()
 {
 return $this->doFlush();
 }
 public function deleteAll()
 {
 $namespaceCacheKey = $this->getNamespaceCacheKey();
 $namespaceVersion = $this->getNamespaceVersion() + 1;
 if ($this->doSave($namespaceCacheKey, $namespaceVersion)) {
 $this->namespaceVersion = $namespaceVersion;
 return \true;
 }
 return \false;
 }
 private function getNamespacedId(string $id) : string
 {
 $namespaceVersion = $this->getNamespaceVersion();
 return sprintf('%s[%s][%s]', $this->namespace, $id, $namespaceVersion);
 }
 private function getNamespaceCacheKey() : string
 {
 return sprintf(self::DOCTRINE_NAMESPACE_CACHEKEY, $this->namespace);
 }
 private function getNamespaceVersion() : int
 {
 if ($this->namespaceVersion !== null) {
 return $this->namespaceVersion;
 }
 $namespaceCacheKey = $this->getNamespaceCacheKey();
 $this->namespaceVersion = (int) $this->doFetch($namespaceCacheKey) ?: 1;
 return $this->namespaceVersion;
 }
 protected function doFetchMultiple(array $keys)
 {
 $returnValues = [];
 foreach ($keys as $key) {
 $item = $this->doFetch($key);
 if ($item === \false && !$this->doContains($key)) {
 continue;
 }
 $returnValues[$key] = $item;
 }
 return $returnValues;
 }
 protected abstract function doFetch($id);
 protected abstract function doContains($id);
 protected function doSaveMultiple(array $keysAndValues, $lifetime = 0)
 {
 $success = \true;
 foreach ($keysAndValues as $key => $value) {
 if ($this->doSave($key, $value, $lifetime)) {
 continue;
 }
 $success = \false;
 }
 return $success;
 }
 protected abstract function doSave($id, $data, $lifeTime = 0);
 protected function doDeleteMultiple(array $keys)
 {
 $success = \true;
 foreach ($keys as $key) {
 if ($this->doDelete($key)) {
 continue;
 }
 $success = \false;
 }
 return $success;
 }
 protected abstract function doDelete($id);
 protected abstract function doFlush();
 protected abstract function doGetStats();
}
