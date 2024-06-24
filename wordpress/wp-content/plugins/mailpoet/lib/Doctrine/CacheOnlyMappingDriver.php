<?php declare(strict_types = 1);

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoet\RuntimeException;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;

/**
 * Intended to be used in production environment where we rely on metadata cache for reading all metadata.
 */
class CacheOnlyMappingDriver implements MappingDriver {
  /** @var string */
  protected $cacheSalt = '__CLASSMETADATA__';

  /** @var CacheItemPoolInterface */
  private $metaDataCache;

  public function __construct(
    CacheItemPoolInterface $metaDataCache
  ) {
    $this->metaDataCache = $metaDataCache;
  }

  /**
   * @inerhitDoc
   */
  public function loadMetadataForClass($className, ClassMetadata $metadata) {
    // We don't need to load anything it is all cached.
  }

  /**
   * @inerhitDoc
   */
  public function getAllClassNames() {
    throw new RuntimeException('CacheOnlyMappingDriver::getAllClassNames should not be called');
  }

  /**
   * @inerhitDoc
   */
  public function isTransient($className) {
    // Everything in cache are metadata and class with metadata is non-transient
    // See https://github.com/doctrine/persistence/blob/b07e347a24e7a19a2b6462e00a6dff899e4c2dd2/src/Persistence/Mapping/Driver/MappingDriver.php#L34
    return !$this->metaDataCache->hasItem($this->getCacheKey($className));
  }

  /**
   * Copy pasted from MailPoetVendor\Doctrine\Persistence\Mapping\AbstractClassMetadataFactory
   */
  protected function getCacheKey(string $className): string {
    return str_replace('\\', '__', $className) . $this->cacheSalt;
  }
}
