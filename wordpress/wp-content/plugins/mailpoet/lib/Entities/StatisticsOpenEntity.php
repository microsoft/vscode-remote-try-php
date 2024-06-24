<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="statistics_opens")
 */
class StatisticsOpenEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
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
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\UserAgentEntity")
   * @var UserAgentEntity|null
   */
  private $userAgent;

  /**
   * @ORM\Column(type="smallint")
   * @var int
   */
  private $userAgentType = 0;

  public function __construct(
    NewsletterEntity $newsletter,
    SendingQueueEntity $queue,
    SubscriberEntity $subscriber
  ) {
    $this->newsletter = $newsletter;
    $this->queue = $queue;
    $this->subscriber = $subscriber;
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
