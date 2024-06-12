<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="subscriber_segment")
 */
class SubscriberSegmentEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SegmentEntity")
   * @var SegmentEntity|null
   */
  private $segment;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SubscriberEntity", inversedBy="subscriberSegments")
   * @var SubscriberEntity|null
   */
  private $subscriber;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $status;

  public function __construct(
    SegmentEntity $segment,
    SubscriberEntity $subscriber,
    string $status
  ) {
    $this->segment = $segment;
    $this->subscriber = $subscriber;
    $this->status = $status;
  }

  /**
   * @return SegmentEntity|null
   */
  public function getSegment() {
    $this->safelyLoadToOneAssociation('segment');
    return $this->segment;
  }

  /**
   * @return SubscriberEntity|null
   */
  public function getSubscriber() {
    $this->safelyLoadToOneAssociation('subscriber');
    return $this->subscriber;
  }

  /**
   * @return string
   */
  public function getStatus(): string {
    return $this->status;
  }

  /**
   * @param string $status
   */
  public function setStatus(string $status) {
    $this->status = $status;
  }
}
