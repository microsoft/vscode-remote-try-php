<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="statistics_clicks")
 */
class StatisticsClickEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterEntity")
   * @ORM\JoinColumn(name="newsletter_id", referencedColumnName="id")
   * @var NewsletterEntity|null
   */
  private $newsletter;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SendingQueueEntity")
   * @ORM\JoinColumn(name="queue_id", referencedColumnName="id")
   * @var SendingQueueEntity|null
   */
  private $queue;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SubscriberEntity")
   * @ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")
   * @var SubscriberEntity|null
   */
  private $subscriber;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterLinkEntity", inversedBy="clicks")
   * @var NewsletterLinkEntity|null
   */
  private $link;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\StatisticsWooCommercePurchaseEntity", mappedBy="click", fetch="EXTRA_LAZY")*
   * @var ArrayCollection<int, StatisticsWooCommercePurchaseEntity>
   */
  private $wooCommercePurchases;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\UserAgentEntity")
   * @var UserAgentEntity|null
   */
  private $userAgent;

  /**
   * @ORM\Column(type="smallint")
   * @var int
   */
  private $userAgentType = 0;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $count;

  public function __construct(
    NewsletterEntity $newsletter,
    SendingQueueEntity $queue,
    SubscriberEntity $subscriber,
    NewsletterLinkEntity $link,
    int $count
  ) {
    $this->newsletter = $newsletter;
    $this->queue = $queue;
    $this->subscriber = $subscriber;
    $this->link = $link;
    $this->count = $count;
    $this->wooCommercePurchases = new ArrayCollection();
  }

  /**
   * @return NewsletterEntity|null
   */
  public function getNewsletter() {
    $this->safelyLoadToOneAssociation('newsletter');
    return $this->newsletter;
  }

  /**
   * @return SendingQueueEntity|null
   */
  public function getQueue() {
    $this->safelyLoadToOneAssociation('queue');
    return $this->queue;
  }

  /**
   * @return NewsletterLinkEntity|null
   */
  public function getLink() {
    $this->safelyLoadToOneAssociation('link');
    return $this->link;
  }

  /**
   * @param NewsletterEntity|null $newsletter
   */
  public function setNewsletter($newsletter) {
    $this->newsletter = $newsletter;
  }

  /**
   * @param SendingQueueEntity|null $queue
   */
  public function setQueue($queue) {
    $this->queue = $queue;
  }

  /**
   * @param SubscriberEntity|null $subscriber
   */
  public function setSubscriber($subscriber) {
    $this->subscriber = $subscriber;
  }

  /**
   * @return SubscriberEntity|null
   */
  public function getSubscriber(): ?SubscriberEntity {
    return $this->subscriber;
  }

  /**
   * @param NewsletterLinkEntity|null $link
   */
  public function setLink($link) {
    $this->link = $link;
  }

  /**
   * @param int $count
   */
  public function setCount(int $count) {
    $this->count = $count;
  }

  /**
   * @return ArrayCollection<int, StatisticsWooCommercePurchaseEntity>
   */
  public function getWooCommercePurchases() {
    return $this->wooCommercePurchases;
  }

  public function getCount(): int {
    return $this->count;
  }

  public function getUserAgent(): ?UserAgentEntity {
    return $this->userAgent;
  }

  public function setUserAgent(?UserAgentEntity $userAgent): void {
    $this->userAgent = $userAgent;
  }

  public function getUserAgentType(): int {
    return $this->userAgentType;
  }

  public function setUserAgentType(int $userAgentType): void {
    $this->userAgentType = $userAgentType;
  }
}
