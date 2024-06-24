<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EntityTraits;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait {
  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var DateTimeInterface|null
   */
  private $createdAt;

  public function getCreatedAt(): ?DateTimeInterface {
    return $this->createdAt;
  }

  public function setCreatedAt(DateTimeInterface $createdAt): void {
    $this->createdAt = $createdAt;
  }
}
