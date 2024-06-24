<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\DeletedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoet\Util\Helpers;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="newsletters")
 */
class NewsletterEntity {
  // types
  const TYPE_AUTOMATION = 'automation';
  const TYPE_AUTOMATION_NOTIFICATION = 'automation_notification';
  const TYPE_AUTOMATION_TRANSACTIONAL = 'automation_transactional';
  const TYPE_STANDARD = 'standard';
  const TYPE_NOTIFICATION = 'notification';
  const TYPE_NOTIFICATION_HISTORY = 'notification_history';
  const TYPE_RE_ENGAGEMENT = 're_engagement';
  const TYPE_WC_TRANSACTIONAL_EMAIL = 'wc_transactional';
  const TYPE_CONFIRMATION_EMAIL_CUSTOMIZER = 'confirmation_email';

  // legacy types, replaced by automations
  const TYPE_AUTOMATIC = 'automatic';
  const TYPE_WELCOME = 'welcome';

  // standard newsletters
  const STATUS_DRAFT = 'draft';
  const STATUS_SCHEDULED = 'scheduled';
  const STATUS_SENDING = 'sending';
  const STATUS_SENT = 'sent';
  const STATUS_CORRUPT = 'corrupt';

  /**
   * Newsletters that their body HTML can get re-generated
   * @see NewsletterSaveController::updateQueue
   */
  const TYPES_WITH_RESETTABLE_BODY = [
    NewsletterEntity::TYPE_STANDARD,
  ];

  /**
   * Newsletters that have additional restrictions for activation and sending
   */
  const CAMPAIGN_TYPES = [
    NewsletterEntity::TYPE_STANDARD,
    NewsletterEntity::TYPE_NOTIFICATION,
    NewsletterEntity::TYPE_NOTIFICATION_HISTORY,
    NewsletterEntity::TYPE_RE_ENGAGEMENT,
  ];

  // automatic newsletters status
  const STATUS_ACTIVE = 'active';

  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use DeletedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $hash;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $subject;

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank()
   * @var string
   */
  private $type;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $senderAddress = '';

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $senderName = '';

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $status = self::STATUS_DRAFT;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $replyToAddress = '';

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $replyToName = '';

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $preheader = '';

  /**
   * @ORM\Column(type="json", nullable=true)
   * @var array|null
   */
  private $body;

  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var DateTimeInterface|null
   */
  private $sentAt;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $unsubscribeToken;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $gaCampaign = '';

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterEntity")
   * @var NewsletterEntity|null
   */
  private $parent;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\NewsletterEntity", mappedBy="parent", fetch="EXTRA_LAZY")
   * @var ArrayCollection<int, NewsletterEntity>
   */
  private $children;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\NewsletterSegmentEntity", mappedBy="newsletter", orphanRemoval=true)
   * @var ArrayCollection<int, NewsletterSegmentEntity>
   */
  private $newsletterSegments;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\NewsletterOptionEntity", mappedBy="newsletter", orphanRemoval=true)
   * @var ArrayCollection<int, NewsletterOptionEntity>
   */
  private $options;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\SendingQueueEntity", mappedBy="newsletter")
   * @var ArrayCollection<int, SendingQueueEntity>
   */
  private $queues;

  /**
   * @ORM\OneToOne(targetEntity="MailPoet\Entities\WpPostEntity")
   * @ORM\JoinColumn(name="wp_post_id", referencedColumnName="ID", nullable=true)
   * @var WpPostEntity|null
   */
  private $wpPost;

  public function __construct() {
    $this->children = new ArrayCollection();
    $this->newsletterSegments = new ArrayCollection();
    $this->options = new ArrayCollection();
    $this->queues = new ArrayCollection();
  }

  /**
   * @deprecated This is here only for backward compatibility with custom shortcodes https://kb.mailpoet.com/article/160-create-a-custom-shortcode
   * This can be removed after 2021-08-01
   */
  public function __get($key) {
    $getterName = 'get' . Helpers::underscoreToCamelCase($key, $capitaliseFirstChar = true);
    $callable = [$this, $getterName];
    if (is_callable($callable)) {
      return call_user_func($callable);
    }
  }

  public function __clone() {
    // reset ID
    $this->id = null;
    $this->newsletterSegments = new ArrayCollection();
    $this->children = new ArrayCollection();
    $this->options = new ArrayCollection();
    $this->queues = new ArrayCollection();
  }

  /**
   * @return string|null
   */
  public function getHash() {
    return $this->hash;
  }

  /**
   * @param string|null $hash
   */
  public function setHash($hash) {
    $this->hash = $hash;
  }

  /**
   * @return string
   */
  public function getSubject() {
    return $this->subject;
  }

  /**
   * @param string $subject
   */
  public function setSubject($subject) {
    $this->subject = $subject;
  }

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getSenderAddress() {
    return $this->senderAddress;
  }

  /**
   * @param string $senderAddress
   */
  public function setSenderAddress($senderAddress) {
    $this->senderAddress = $senderAddress;
  }

  /**
   * @return string
   */
  public function getSenderName() {
    return $this->senderName;
  }

  /**
   * @param string $senderName
   */
  public function setSenderName($senderName) {
    $this->senderName = $senderName;
  }

  /**
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param string $status
   */
  public function setStatus($status) {
    $this->status = $status;

    // activate/deactivate unfinished tasks
    $newTaskStatus = null;
    $typesWithActivation = [self::TYPE_NOTIFICATION, self::TYPE_WELCOME, self::TYPE_AUTOMATIC];

    if (($status === self::STATUS_DRAFT) && in_array($this->type, $typesWithActivation)) {
      $newTaskStatus = ScheduledTaskEntity::STATUS_PAUSED;
    }
    if (($status === self::STATUS_ACTIVE) && in_array($this->type, $typesWithActivation)) {
      $newTaskStatus = ScheduledTaskEntity::STATUS_SCHEDULED;
    }

    if (!$newTaskStatus) return;

    $queues = $this->getUnfinishedQueues();

    foreach ($queues as $queue) {
      /** @var SendingQueueEntity $queue */
      $task = $queue->getTask();
      if ($task === null) continue;

      $scheduled = new Carbon($task->getScheduledAt());
      if ($scheduled < (new Carbon())->subDays(30)) continue;

      if (($status === self::STATUS_DRAFT) && ($task->getStatus() !== ScheduledTaskEntity::STATUS_SCHEDULED)) continue;
      if (($status === self::STATUS_ACTIVE) && ($task->getStatus() !== ScheduledTaskEntity::STATUS_PAUSED)) continue;

      $task->setStatus($newTaskStatus);
    }
  }

  /**
   * @return string
   */
  public function getReplyToAddress() {
    return $this->replyToAddress;
  }

  /**
   * @param string $replyToAddress
   */
  public function setReplyToAddress($replyToAddress) {
    $this->replyToAddress = $replyToAddress;
  }

  /**
   * @return string
   */
  public function getReplyToName() {
    return $this->replyToName;
  }

  /**
   * @param string $replyToName
   */
  public function setReplyToName($replyToName) {
    $this->replyToName = $replyToName;
  }

  /**
   * @return string
   */
  public function getPreheader() {
    return $this->preheader;
  }

  /**
   * @param string $preheader
   */
  public function setPreheader($preheader) {
    $this->preheader = $preheader;
  }

  /**
   * @return array|null
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * @param array|null $body
   */
  public function setBody($body) {
    $this->body = $body;
  }

  /**
   * @return DateTimeInterface|null
   */
  public function getSentAt() {
    return $this->sentAt;
  }

  /**
   * @param DateTimeInterface|null $sentAt
   */
  public function setSentAt($sentAt) {
    $this->sentAt = $sentAt;
  }

  /**
   * @return string|null
   */
  public function getUnsubscribeToken() {
    return $this->unsubscribeToken;
  }

  /**
   * @return string
   */
  public function getGaCampaign() {
    return $this->gaCampaign;
  }

  /**
   * @param string $gaCampaign
   */
  public function setGaCampaign($gaCampaign) {
    $this->gaCampaign = $gaCampaign;
  }

  /**
   * @param string|null $unsubscribeToken
   */
  public function setUnsubscribeToken($unsubscribeToken) {
    $this->unsubscribeToken = $unsubscribeToken;
  }

  /**
   * @return NewsletterEntity|null
   */
  public function getParent() {
    $this->safelyLoadToOneAssociation('parent');
    return $this->parent;
  }

  /**
   * @param NewsletterEntity|null $parent
   */
  public function setParent($parent) {
    $this->parent = $parent;
  }

  /**
   * @return ArrayCollection<int, NewsletterEntity>
   */
  public function getChildren() {
    return $this->children;
  }

  /**
   * @return ArrayCollection<int, NewsletterSegmentEntity>
   */
  public function getNewsletterSegments() {
    return $this->newsletterSegments;
  }

  /**
   * @return int[]
   */
  public function getSegmentIds() {
    return array_filter($this->newsletterSegments->map(function(NewsletterSegmentEntity $newsletterSegment = null) {
      if (!$newsletterSegment) return null;
      $segment = $newsletterSegment->getSegment();
      return $segment ? (int)$segment->getId() : null;
    })->toArray());
  }

  /**
   * @return ArrayCollection<int, NewsletterOptionEntity>
   */
  public function getOptions() {
    return $this->options;
  }

  public function getOption(string $name): ?NewsletterOptionEntity {
    $option = $this->options->filter(function (NewsletterOptionEntity $option = null) use ($name): bool {
      if (!$option) return false;
      return ($field = $option->getOptionField()) ? $field->getName() === $name : false;
    })->first();
    return $option ?: null;
  }

  /**
   * @return array<string, mixed> Associative array of newsletter option values with option names as keys
   */
  public function getOptionsAsArray(): array {
    $optionsArray = [];
    foreach ($this->options as $option) {
      $name = $option->getName();
      if (!$name) {
        continue;
      }
      $optionsArray[$name] = $option->getValue();
    }
    return $optionsArray;
  }

  public function getOptionValue(string $name) {
    $option = $this->getOption($name);
    return $option ? $option->getValue() : null;
  }

  public function getFilterSegmentId(): ?int {
    $optionValue = $this->getOptionValue(NewsletterOptionFieldEntity::NAME_FILTER_SEGMENT_ID);
    if ($optionValue) {
      return (int)$optionValue;
    }
    $parentNewsletter = $this->getParent();
    if ($parentNewsletter instanceof NewsletterEntity && $this->getId() !== $parentNewsletter->getId()) {
      return $parentNewsletter->getFilterSegmentId();
    }
    return null;
  }

  /**
   * @return ArrayCollection<int, SendingQueueEntity>
   */
  public function getQueues() {
    return $this->queues;
  }

  public function getLatestQueue(): ?SendingQueueEntity {
    $criteria = new Criteria();
    $criteria->orderBy(['id' => Criteria::DESC]);
    $criteria->setMaxResults(1);
    return $this->queues->matching($criteria)->first() ?: null;
  }

  public function getLastUpdatedQueue(): ?SendingQueueEntity {
    $criteria = new Criteria();
    $criteria->orderBy(['updatedAt' => Criteria::DESC]);
    $criteria->setMaxResults(1);
    return $this->queues->matching($criteria)->first() ?: null;
  }

  /**
   * @return Collection<int, SendingQueueEntity>
   */
  private function getUnfinishedQueues(): Collection {
    $criteria = new Criteria();
    $expr = Criteria::expr();
    $criteria->where($expr->neq('countToProcess', 0));
    return $this->queues->matching($criteria);
  }

  public function getGlobalStyle(string $category, string $style): ?string {
    $body = $this->getBody();
    if ($body === null) {
      return null;
    }
    return $body['globalStyles'][$category][$style] ?? null;
  }

  public function getProcessedAt(): ?DateTimeInterface {
    $processedAt = null;
    $queue = $this->getLatestQueue();

    if ($queue instanceof SendingQueueEntity) {
      $task = $queue->getTask();

      if ($task instanceof ScheduledTaskEntity) {
        $processedAt = $task->getProcessedAt();
      }
    }

    return $processedAt;
  }

  public function getContent(): string {
    $content = $this->getBody()['content'] ?? '';
    return json_encode($content) ?: '';
  }

  /**
   * Only some types of newsletters can be set as sent. Some others are just active or draft.
   */
  public function canBeSetSent(): bool {
    return in_array($this->getType(), [self::TYPE_NOTIFICATION_HISTORY, self::TYPE_STANDARD], true);
  }

  public function getWpPost(): ?WpPostEntity {
    $this->safelyLoadToOneAssociation('wpPost');
    return $this->wpPost;
  }

  public function setWpPost(?WpPostEntity $wpPostEntity): void {
    $this->wpPost = $wpPostEntity;
  }

  public function getWpPostId(): ?int {
    $wpPost = $this->wpPost;
    return $wpPost ? $wpPost->getId() : null;
  }

  public function getCampaignName(): ?string {
    $wpPost = $this->getWpPost();
    if (!$wpPost) {
      return null;
    }
    return $wpPost->getPostTitle();
  }

  /**
   * Used for cases when we present newsletter by name.
   * Newsletters created via legacy editor have only subjects.
   */
  public function getCampaignNameOrSubject(): string {
    $campaignName = $this->getCampaignName();
    return $campaignName ?: $this->getSubject();
  }
}
