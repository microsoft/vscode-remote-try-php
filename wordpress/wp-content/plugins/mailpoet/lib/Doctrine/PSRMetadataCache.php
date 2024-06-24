<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Psr\Cache\CacheItemInterface;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;

class PSRMetadataCache implements CacheItemPoolInterface {
  /** @var MetadataCache */
  private $metadataCache;

  public function __construct(
    string $dir,
    bool $isReadOnly
  ) {
    $this->metadataCache = new MetadataCache($dir, $isReadOnly);
  }

  /**
   * @inheritDoc
   */
  public function getItem($key): CacheItemInterface {
    if (!$this->hasItem($key)) {
      return new PSRCacheItem($key, false);
    }
    $item = new PSRCacheItem($key, true);
    $item->set($this->metadataCache->fetch($key));
    return $item;
  }

  /**
   * @inheritDoc
   */
  public function getItems(array $keys = []) {
    if (empty($keys)) {
      return [];
    }
    $foundItems = [];
    // no internal array function supports this sort of mapping: needs to be iterative
    // this filters and combines keys in one pass
    foreach ($keys as $key) {
      if (!is_string($key)) {
        throw new PSRCacheInvalidArgumentException('Invalid key');
      }
      $foundItems[$key] = $this->getItem($key);
    }
    return $foundItems;
  }

  /**
   * @inheritDoc
   */
  public function hasItem($key): bool {
    return $this->metadataCache->contains($key);
  }

  /**
   * @inheritDoc
   */
  public function clear(): bool {
    return $this->metadataCache->flushAll();
  }

  /**
   * @inheritDoc
   */
  public function deleteItem($key): bool {
    return $this->metadataCache->delete($key);
  }

  /**
   * @inheritDoc
   */
  public function deleteItems(array $keys): bool {
    if (empty($keys)) {
      return true;
    }
    foreach ($keys as $key) {
      $this->deleteItem($key);
    }
    return true;
  }

  /**
   * @inheritDoc
   */
  public function save(CacheItemInterface $item) {
    try {
      return $this->metadataCache->save($item->getKey(), $item->get());
    } catch (\RuntimeException $e) {
      throw new PSRCacheInvalidArgumentException($e->getMessage(), $e->getCode(), $e);
    }
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
  public function commit(): bool {
    return true;
  }
}
