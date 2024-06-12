<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="statistics_unsubscribes")
 */
class StatisticsUnsubscribeEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  const SOURCE_NEWSLETTER = 'newsletter';
  const SOURCE_MANAGE = 'manage';
  const SOURCE_ADMINISTRATOR = 'admin';
  const SOURCE_ORDER_CHECKOUT = 'order_checkout';
  const SOURCE_AUTOMATION = 'automation';
  const SOURCE_MP_API = 'mp_api';

  const METHOD_LINK = 'link';
  const METHOD_ONE_CLICK = 'one_click';
  const METHOD_UNKNOWN = 'unknown';

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
   * @ORM\Column(type="string")
   * @var string
   */
  private $source = 'unknown';

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $meta;

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  private $method = self::METHOD_UNKNOWN;

  public function __construct(
    NewsletterEntity $newsletter = null,
    SendingQueueEntity $queue = null,
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
   * @return string
   */
  public function getSource(): string {
    return $this->source;
  }

  /**
   * @param string $source
   */
  public function setSource(string $source) {
    $this->source = $source;
  }

  /**
   * @param string $meta
   */
  public function setMeta(string $meta) {
    $this->meta = $meta;
  }

  /**
   * @return string|null
   */
  public function getMeta() {
    return $this->meta;
  }

  public function setMethod(string $method) {
    $this->method = $method;
  }

  public function getMethod(): string {
    return $this->method;
  }
}
