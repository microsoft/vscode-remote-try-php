<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Subscribers\EngagementDataBackfiller;
use MailPoet\WP\Functions as WPFunctions;

class BackfillEngagementData extends SimpleWorker {
  const TASK_TYPE = 'backfill_engagement_data';
  const BATCH_SIZE = 100;
  const AUTOMATIC_SCHEDULING = false;
  const SUPPORT_MULTIPLE_INSTANCES = false;

  /** @var EngagementDataBackfiller */
  private $engagementDataBackfiller;

  public function __construct(
    EngagementDataBackfiller $engagementDataBackfiller,
    WPFunctions $wp
  ) {
    parent::__construct($wp);
    $this->engagementDataBackfiller = $engagementDataBackfiller;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $meta = $task->getMeta();

    $lastSubscriberId = $meta['last_subscriber_id'] ?? 0;

    do {
      $this->cronHelper->enforceExecutionLimit($timer);
      $batch = $this->engagementDataBackfiller->getBatch($lastSubscriberId, self::BATCH_SIZE);
      if (empty($batch)) {
        break;
      }
      $this->engagementDataBackfiller->updateBatch($batch);
      $lastSubscriberId = $this->engagementDataBackfiller->getLastProcessedSubscriberId();
      $meta['last_subscriber_id'] = $lastSubscriberId;
      $task->setMeta($meta);
      $this->scheduledTasksRepository->persist($task);
      $this->scheduledTasksRepository->flush();
    } while (count($batch) === self::BATCH_SIZE);

    return true;
  }
}
