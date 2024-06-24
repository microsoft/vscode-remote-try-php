<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Psr\Cache\CacheItemInterface;

class PSRCacheItem implements CacheItemInterface {

  /** @var string */
  private $key;

  /** @var mixed */
  private $value;

  /** @var bool */
  private $isHit;

  public function __construct(
    string $key,
    bool $isHit
  ) {
    $this->key = $key;
    $this->isHit = $isHit;
  }

  /**
   * @inheritDoc
   */
  public function getKey(): string {
    return $this->key;
  }

  /**
   * @inheritDoc
   */
  public function get() {
    return $this->value;
  }

  /**
   * @inheritDoc
   */
  public function isHit(): bool {
    // TODO: Implement isHit() method.
    return $this->isHit;
  }

  /**
   * @inheritDoc
   */
  public function set($value) {
    $this->value = $value;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function expiresAt($expiration) {
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function expiresAfter($time) {
    return $this;
  }
}
