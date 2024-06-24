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
 * @ORM\Table(name="statistics_woocommerce_purchases")
 */
class StatisticsWooCommercePurchaseEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterEntity")
   * @ORM\JoinColumn(name="newsletter_id", referencedColumnName="id", nullable=true)
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
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\StatisticsClickEntity", inversedBy="wooCommercePurchases")
   * @ORM\JoinColumn(name="click_id", referencedColumnName="id")
   * @var StatisticsClickEntity|null
   */
  private $click;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $orderId;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $orderCurrency;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  private $orderPriceTotal;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $status;

  public function __construct(
    NewsletterEntity $newsletter,
    SendingQueueEntity $queue,
    StatisticsClickEntity $click,
    int $orderId,
    string $orderCurrency,
    float $orderPriceTotal,
    string $status
  ) {
    $this->newsletter = $newsletter;
    $this->queue = $queue;
    $this->click = $click;
    $this->orderId = $orderId;
    $this->orderCurrency = $orderCurrency;
    $this->orderPriceTotal = $orderPriceTotal;
    $this->status = $status;
  }

  public function getNewsletter(): ?NewsletterEntity {
    $this->safelyLoadToOneAssociation('newsletter');
    return $this->newsletter;
  }

  public function getQueue(): ?SendingQueueEntity {
    $this->safelyLoadToOneAssociation('queue');
    return $this->queue;
  }

  public function getSubscriber(): ?SubscriberEntity {
    $this->safelyLoadToOneAssociation('subscriber');
    return $this->subscriber;
  }

  public function getClick(): ?StatisticsClickEntity {
    $this->safelyLoadToOneAssociation('click');
    return $this->click;
  }

  public function getOrderId(): int {
    return $this->orderId;
  }

  public function setSubscriber(?SubscriberEntity $subscriber) {
    $this->subscriber = $subscriber;
  }

  public function getOrderCurrency(): string {
    return $this->orderCurrency;
  }

  public function getOrderPriceTotal(): float {
    return $this->orderPriceTotal;
  }

  public function setOrderCurrency(string $orderCurrency): void {
    $this->orderCurrency = $orderCurrency;
  }

  public function setOrderPriceTotal(float $orderPriceTotal): void {
    $this->orderPriceTotal = $orderPriceTotal;
  }

  public function getStatus(): string {
    return $this->status;
  }

  public function setStatus(string $status): void {
    $this->status = $status;
  }
}
