<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EntityTraits;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

trait DeletedAtTrait {
  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var DateTimeInterface|null
   */
  private $deletedAt;

  /** @return DateTimeInterface|null */
  public function getDeletedAt() {
    return $this->deletedAt;
  }

  /** @param DateTimeInterface|null $deletedAt */
  public function setDeletedAt($deletedAt) {
    $this->deletedAt = $deletedAt;
  }
}
