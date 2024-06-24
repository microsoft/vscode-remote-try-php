<?php
namespace MailPoetVendor\Doctrine\Common\Cache\Psr6;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\Common\Cache\CacheProvider;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use MailPoetVendor\Symfony\Component\Cache\Adapter\DoctrineAdapter as SymfonyDoctrineAdapter;
use MailPoetVendor\Symfony\Contracts\Service\ResetInterface;
use function rawurlencode;
final class DoctrineProvider extends CacheProvider
{
 private $pool;
 public static function wrap(CacheItemPoolInterface $pool) : Cache
 {
 if ($pool instanceof CacheAdapter) {
 return $pool->getCache();
 }
 if ($pool instanceof SymfonyDoctrineAdapter) {
 $getCache = function () {
 // phpcs:ignore Squiz.Scope.StaticThisUsage.Found
 return $this->provider;
 };
 return $getCache->bindTo($pool, SymfonyDoctrineAdapter::class)();
 }
 return new self($pool);
 }
 private function __construct(CacheItemPoolInterface $pool)
 {
 $this->pool = $pool;
 }
 public function getPool() : CacheItemPoolInterface
 {
 return $this->pool;
 }
 public function reset() : void
 {
 if ($this->pool instanceof ResetInterface) {
 $this->pool->reset();
 }
 $this->setNamespace($this->getNamespace());
 }
 protected function doFetch($id)
 {
 $item = $this->pool->getItem(rawurlencode($id));
 return $item->isHit() ? $item->get() : \false;
 }
 protected function doContains($id)
 {
 return $this->pool->hasItem(rawurlencode($id));
 }
 protected function doSave($id, $data, $lifeTime = 0)
 {
 $item = $this->pool->getItem(rawurlencode($id));
 if (0 < $lifeTime) {
 $item->expiresAfter($lifeTime);
 }
 return $this->pool->save($item->set($data));
 }
 protected function doDelete($id)
 {
 return $this->pool->deleteItem(rawurlencode($id));
 }
 protected function doFlush()
 {
 return $this->pool->clear();
 }
 protected function doGetStats()
 {
 return null;
 }
}
