<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Subscribers\SubscribersEmailCountsController;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SubscribersEmailCount extends SimpleWorker {
  const TASK_TYPE = 'subscribers_email_count';
  const BATCH_SIZE = 1000;
  const SUPPORT_MULTIPLE_INSTANCES = false;

  /** @var SubscribersEmailCountsController */
  private $subscribersEmailCountsController;

  /** @var EntityManager */
  private $entityManager;

  /** @var SettingsController */
  private $settings;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    SubscribersEmailCountsController $subscribersEmailCountsController,
    EntityManager $entityManager,
    SettingsController $settings,
    TrackingConfig $trackingConfig
  ) {
    $this->subscribersEmailCountsController = $subscribersEmailCountsController;
    $this->entityManager = $entityManager;
    $this->settings = $settings;
    $this->trackingConfig = $trackingConfig;
    parent::__construct();
  }

  public function checkProcessingRequirements() {
    if (!$this->trackingConfig->isEmailTrackingEnabled()) {
      return false;
    }

    $daysToInactive = (int)$this->settings->get('deactivate_subscriber_after_inactive_days');
    if ($daysToInactive === 0) {
      return false;
    }

    return true;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $previousTask = $this->findPreviousTask($task);
    $dateFromLastRun = null;
    if ($previousTask instanceof ScheduledTaskEntity) {
      $dateFromLastRun = $previousTask->getScheduledAt();
    }

    $meta = $task->getMeta();
    $lastSubscriberId = isset($meta['last_subscriber_id']) ? (int)$meta['last_subscriber_id'] : 0;
    $highestSubscriberId = isset($meta['highest_subscriber_id']) ? (int)$meta['highest_subscriber_id'] : $this->getHighestSubscriberId();
    $meta['highest_subscriber_id'] = $highestSubscriberId;
    $task->setMeta($meta);

    while ($lastSubscriberId <= $highestSubscriberId) {
      [$count, $lastSubscriberId] = $this->subscribersEmailCountsController->updateSubscribersEmailCounts($dateFromLastRun, self::BATCH_SIZE, intval($lastSubscriberId));
      if ($count === 0) {
        break;
      }

      $meta['last_subscriber_id'] = $lastSubscriberId++;
      $task->setMeta($meta);
      $this->scheduledTasksRepository->persist($task);
      $this->scheduledTasksRepository->flush();
      $this->cronHelper->enforceExecutionLimit($timer);
    };

    $this->schedule();
    return true;
  }

  private function findPreviousTask(ScheduledTaskEntity $task): ?ScheduledTaskEntity {
    return $this->scheduledTasksRepository->findPreviousTask($task);
  }

  private function getHighestSubscriberId(): int {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $result = $this->entityManager->getConnection()->executeQuery("SELECT MAX(id) FROM $subscribersTable LIMIT 1;")->fetchNumeric();
    /** @var int[] $result - it's required for PHPStan */
    return is_array($result) && isset($result[0]) ? (int)$result[0] : 0;
  }
}
