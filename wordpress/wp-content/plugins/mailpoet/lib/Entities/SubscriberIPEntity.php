<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="subscriber_ips")
 */
class SubscriberIPEntity {
  /**
   * @ORM\Id
   * @ORM\Column(type="string")
   * @var string
   */
  private $ip;

  /**
   * We have to use own type because createdAt is part of the primary key and basic datetimetz isn't supported in primary key
   * @ORM\Id
   * @ORM\Column(type="datetimetz_to_string")
   * @var DateTimeInterface
   */
  private $createdAt;

  public function __construct(
    string $ip
  ) {
    $this->ip = $ip;
    $this->createdAt = new Carbon();
  }

  public function getIP(): string {
    return $this->ip;
  }

  public function getCreatedAt(): DateTimeInterface {
    return $this->createdAt;
  }

  public function setCreatedAt(DateTimeInterface $createdAt): void {
    $this->createdAt = $createdAt;
  }
}
