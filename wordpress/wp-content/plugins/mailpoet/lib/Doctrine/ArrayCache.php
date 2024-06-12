<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\Common\Cache\CacheProvider;

/**
 * Array cache
 * Based on https://github.com/doctrine/cache/blob/1.11.x/lib/Doctrine/Common/Cache/ArrayCache.php
 * The cache implementation was removed from the doctrine/cache v2.0 so we need to provide own implementation.
 */
class ArrayCache extends CacheProvider {

  /** @var mixed[] */
  private $data = [];

  /** @var int */
  private $hitsCount = 0;

  /** @var int */
  private $missesCount = 0;

  /** @var int */
  private $upTime;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->upTime = time();
  }

  /**
   * {@inheritdoc}
   */
  protected function doFetch($id) {
    if (!$this->doContains($id)) {
      $this->missesCount += 1;
      return false;
    }
    $this->hitsCount += 1;
    return $this->data[$id][0];
  }

  /**
   * {@inheritdoc}
   */
  protected function doContains($id) {
    if (!isset($this->data[$id])) {
      return false;
    }
    $expiration = $this->data[$id][1];
    if ($expiration && $expiration < \time()) {
      $this->doDelete($id);
      return false;
    }
    return true;
  }

  /**
   * {@inheritdoc}
   */
  protected function doSave($id, $data, $lifeTime = 0) {
    $this->data[$id] = [$data, $lifeTime ? \time() + $lifeTime : false];
    return true;
  }

  /**
   * {@inheritdoc}
   */
  protected function doDelete($id) {
    unset($this->data[$id]);
    return true;
  }

  /**
   * {@inheritdoc}
   */
  protected function doFlush() {
    $this->data = [];
    return true;
  }

  /**
   * {@inheritdoc}
   */
  protected function doGetStats() {
    return [
      CacheProvider::STATS_HITS => $this->hitsCount,
      CacheProvider::STATS_MISSES => $this->missesCount,
      CacheProvider::STATS_UPTIME => $this->upTime,
      CacheProvider::STATS_MEMORY_USAGE => null,
      CacheProvider::STATS_MEMORY_AVAILABLE => null,
    ];
  }
}
