<?php declare(strict_types = 1);

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="subscriber_tag")
 */
class SubscriberTagEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\TagEntity")
   * @var TagEntity|null
   */
  private $tag;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SubscriberEntity", inversedBy="subscriberTags")
   * @var SubscriberEntity|null
   */
  private $subscriber;

  public function __construct(
    TagEntity $tag,
    SubscriberEntity $subscriber
  ) {
    $this->tag = $tag;
    $this->subscriber = $subscriber;
  }

  public function getTag(): ?TagEntity {
    $this->safelyLoadToOneAssociation('tag');
    return $this->tag;
  }

  public function getSubscriber(): ?SubscriberEntity {
    $this->safelyLoadToOneAssociation('subscriber');
    return $this->subscriber;
  }
}
