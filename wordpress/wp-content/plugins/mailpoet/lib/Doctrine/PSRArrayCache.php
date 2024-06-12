<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Psr\Cache\CacheItemInterface;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;

class PSRArrayCache implements CacheItemPoolInterface {
  /** @var mixed[] */
  private $cache = [];

  /**
   * @inheritDoc
   */
  public function getItem($key) {
    if (!is_string($key)) {
      throw new PSRCacheInvalidArgumentException('Invalid key');
    }
    if (!$this->hasItem($key)) {
      return new PSRCacheItem($key, false);
    }
    return new PSRCacheItem($key, $this->cache[$key]);
  }

  public function getItems(array $keys = []) {
    return array_map([$this, 'getItem'], $keys);
  }

  /**
   * @inheritDoc
   */
  public function hasItem($key) {
    return array_key_exists($key, $this->cache);
  }

  /**
   * @inheritDoc
   */
  public function clear() {
    $this->cache = [];
    return true;
  }

  /**
   * @inheritDoc
   */
  public function deleteItem($key) {
    if (!is_string($key)) {
      throw new PSRCacheInvalidArgumentException('Invalid key');
    }
    unset($this->cache[$key]);
    return true;
  }

  /**
   * @inheritDoc
   */
  public function deleteItems(array $keys) {
    try {
      array_map([$this, 'deleteItem'], $keys);
    } catch (PSRCacheInvalidArgumentException $e) {
      return false;
    }
    return true;
  }

  /**
   * @inheritDoc
   */
  public function save(CacheItemInterface $item) {
    $this->cache[$item->getKey()] = $item->get();
    return true;
  }

  /**
   * @inheritDoc
   */
  public function saveDeferred(CacheItemInterface $item) {
    return $this->save($item);
  }

  /**
   * @inheritDoc
   */
  public function commit() {
    return true;
  }
}
