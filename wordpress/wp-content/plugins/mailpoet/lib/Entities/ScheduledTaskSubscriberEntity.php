<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="scheduled_task_subscribers")
 */
class ScheduledTaskSubscriberEntity {
  const STATUS_UNPROCESSED = 0;
  const STATUS_PROCESSED = 1;

  const FAIL_STATUS_OK = 0;
  const FAIL_STATUS_FAILED = 1;

  const SENDING_STATUS_SENT = 'sent';
  const SENDING_STATUS_FAILED = 'failed';
  const SENDING_STATUS_UNPROCESSED = 'unprocessed';

  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $processed;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $failed;

  /**
   * @ORM\Column(type="text", nullable=true)
   * @var string|null
   */
  private $error;

  /**
   * @ORM\Id @ORM\ManyToOne(targetEntity="MailPoet\Entities\ScheduledTaskEntity", inversedBy="subscribers")
   * @var ScheduledTaskEntity|null
   */
  private $task;

  /**
   * @ORM\Id @ORM\ManyToOne(targetEntity="MailPoet\Entities\SubscriberEntity", inversedBy="scheduledTaskSubscribers")
   * @var SubscriberEntity|null
   */
  private $subscriber;

  public function __construct(
    ScheduledTaskEntity $task,
    SubscriberEntity $subscriber,
    int $processed = 0,
    int $failed = 0,
    string $error = null
  ) {
    $this->task = $task;
    $this->subscriber = $subscriber;
    $this->processed = $processed;
    $this->failed = $failed;
    $this->error = $error;
  }

  public function getProcessed(): int {
    return $this->processed;
  }

  public function setProcessed(int $processed) {
    $this->processed = $processed;
  }

  public function getFailed(): int {
    return $this->failed;
  }

  public function setFailed(int $failed) {
    $this->failed = $failed;
  }

  /**
   * @return string|null
   */
  public function getError() {
    return $this->error;
  }

  /**
   * @param string|null $error
   */
  public function setError($error) {
    $this->error = $error;
  }

  /**
   * @return ScheduledTaskEntity|null
   */
  public function getTask() {
    $this->safelyLoadToOneAssociation('task');
    return $this->task;
  }

  public function setTask(ScheduledTaskEntity $task) {
    $this->task = $task;
  }

  /**
   * @return SubscriberEntity|null
   */
  public function getSubscriber() {
    $this->safelyLoadToOneAssociation('subscriber');
    return $this->subscriber;
  }

  /**
   * Get the ID of the subscriber without querying wp_mailpoet_subscribers.
   * $this->getSubscriber->getId() queries wp_mailpoet_subscribers because of
   * the way the SafeToOneAssociationLoadTrait works.
   *
   * @return int|null
   */
  public function getSubscriberId() {
    if ($this->subscriber instanceof SubscriberEntity) {
      return $this->subscriber->getId();
    }

    return null;
  }

  public function setSubscriber(SubscriberEntity $subscriber) {
    $this->subscriber = $subscriber;
  }

  public function resetToUnprocessed() {
    $this->setError(null);
    $this->setProcessed(0);
    $this->setFailed(0);
  }
}
