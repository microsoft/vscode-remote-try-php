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
 * @ORM\Table(name="newsletter_links")
 */
class NewsletterLinkEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  public const UNSUBSCRIBE_LINK_SHORT_CODE = '[link:subscription_unsubscribe_url]';
  public const INSTANT_UNSUBSCRIBE_LINK_SHORT_CODE = '[link:subscription_instant_unsubscribe_url]';
  public const UNSUBSCRIBE_LINK_SHORTCODES = [self::UNSUBSCRIBE_LINK_SHORT_CODE, self::INSTANT_UNSUBSCRIBE_LINK_SHORT_CODE];

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
   * @ORM\Column(type="string")
   * @var string
   */
  private $url;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $hash;

  /**
   * Extra lazy is here for `getTotalClicksCount`.
   * If we didn't specify extra lazy the function would load all clicks and count them. This way it uses a single count query.
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\StatisticsClickEntity", mappedBy="link", fetch="EXTRA_LAZY")
   *
   * @var ArrayCollection<int, StatisticsClickEntity>
   */
  private $clicks;

  public function __construct(
    NewsletterEntity $newsletter,
    SendingQueueEntity $queue,
    string $url,
    string $hash
  ) {
    $this->newsletter = $newsletter;
    $this->queue = $queue;
    $this->url = $url;
    $this->hash = $hash;
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

  public function getUrl(): string {
    return $this->url;
  }

  public function getHash(): string {
    return $this->hash;
  }

  /**
   * @return int
   */
  public function getTotalClicksCount() {
    return $this->clicks->count();
  }

  public function toArray(): array {
    return [
      'id' => $this->getId(),
      'newsletter_id' => ($this->getNewsletter() instanceof NewsletterEntity) ? $this->getNewsletter()->getId() : null,
      'queue_id' => ($this->getQueue() instanceof SendingQueueEntity) ? $this->getQueue()->getId() : null,
      'url' => $this->getUrl(),
      'hash' => $this->getHash(),
      'created_at' => $this->getCreatedAt(),
      'updated_at' => $this->getUpdatedAt(),
    ];
  }
}
