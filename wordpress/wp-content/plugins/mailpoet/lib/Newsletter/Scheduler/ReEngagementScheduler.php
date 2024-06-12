<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Scheduler;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\SendingQueue;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class ReEngagementScheduler {

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var EntityManager */
  private $entityManager;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    NewslettersRepository $newslettersRepository,
    ScheduledTasksRepository $scheduledTasksRepository,
    EntityManager $entityManager,
    WPFunctions $wp
  ) {
    $this->newslettersRepository = $newslettersRepository;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->entityManager = $entityManager;
    $this->wp = $wp;
  }

  /**
   * Schedules sending tasks for re-engagement emails
   * @return ScheduledTaskEntity[]
   */
  public function scheduleAll(): array {
    $scheduled = [];
    $emails = $this->newslettersRepository->findActiveByTypes([NewsletterEntity::TYPE_RE_ENGAGEMENT]);
    if (!$emails) {
      return $scheduled;
    }
    foreach ($emails as $email) {
      $scheduled[] = $this->scheduleForEmail($email);
    }
    return array_filter($scheduled);
  }

  private function scheduleForEmail(NewsletterEntity $newsletter): ?ScheduledTaskEntity {
    $scheduledOrRunning = $this->scheduledTasksRepository->findByScheduledAndRunningForNewsletter($newsletter);
    if ($scheduledOrRunning) {
      return null;
    }
    $intervalUnit = $newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_AFTER_TIME_TYPE);
    $intervalValue = (int)$newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_AFTER_TIME_NUMBER);
    if (!$intervalValue || !in_array($intervalUnit, ['weeks', 'months'], true)) {
      return null;
    }
    if (!$newsletter->getNewsletterSegments()->count()) {
      return null;
    }

    $scheduledTask = $this->scheduleTask();
    $enqueuedCount = 0;
    foreach ($newsletter->getSegmentIds() as $segmentId) {
      $enqueuedCount += $this->enqueueSubscribersForSegment((int)$newsletter->getId(), $segmentId, $scheduledTask, $intervalUnit, $intervalValue);
    }

    if ($enqueuedCount) {
      $this->createSendingQueue($newsletter, $scheduledTask, $enqueuedCount);
      return $scheduledTask;
    } else {
      // Nothing to send
      $this->scheduledTasksRepository->remove($scheduledTask);
      $this->scheduledTasksRepository->flush();
      return null;
    }
  }

  private function scheduleTask(): ScheduledTaskEntity {
    // Scheduled task
    $scheduledTask = new ScheduledTaskEntity();
    $scheduledTask->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $scheduledTask->setScheduledAt(Carbon::createFromTimestamp($this->wp->currentTime('timestamp')));
    $scheduledTask->setType(SendingQueue::TASK_TYPE);
    $scheduledTask->setPriority(SendingQueueEntity::PRIORITY_MEDIUM);
    $this->scheduledTasksRepository->persist($scheduledTask);
    $this->scheduledTasksRepository->flush();
    return $scheduledTask;
  }

  private function createSendingQueue(NewsletterEntity $newsletter, ScheduledTaskEntity $scheduledTask, int $countToProcess): SendingQueueEntity {
    // Sending queue
    $sendingQueue = new SendingQueueEntity();
    $sendingQueue->setTask($scheduledTask);
    $sendingQueue->setNewsletter($newsletter);
    $sendingQueue->setCountToProcess($countToProcess);
    $sendingQueue->setCountTotal($countToProcess);
    $this->entityManager->persist($sendingQueue);
    $this->entityManager->flush();
    return $sendingQueue;
  }

  /**
   * Finds subscribers that should receive re-engagement email and saves scheduled tasks subscribers
   * @return int Count of enqueued subscribers
   */
  private function enqueueSubscribersForSegment(int $newsletterId, int $segmentId, ScheduledTaskEntity $scheduledTask, string $intervalUnit, int $intervalValue): int {
    // Parameters for scheduled task subscribers query
    $thresholdDate = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    if ($intervalUnit === 'months') {
      $thresholdDate->subMonths($intervalValue);
    } else {
      $thresholdDate->subWeeks($intervalValue);
    }
    $thresholdDateSql = $thresholdDate->toDateTimeString();
    // When checking engagement, we ignore emails that subscribers received in the last 24 hours so that we leave them some time to engage.
    // This is prevention for sending re-engagement emails to subscribers who have received a single email very recently.
    $upperThresholdDate = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $upperThresholdDate->subDay();
    $upperThresholdDate = $upperThresholdDate->toDateTimeString();
    $taskId = $scheduledTask->getId();
    $subscribedStatus = SubscriberEntity::STATUS_SUBSCRIBED;
    $newsletterStatsTable = $this->entityManager->getClassMetadata(StatisticsNewsletterEntity::class)->getTableName();
    $scheduledTaskSubscribersTable = $this->entityManager->getClassMetadata(ScheduledTaskSubscriberEntity::class)->getTableName();
    $subscriberSegmentTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $nowSql = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))->toDateTimeString();

    $query = "INSERT IGNORE INTO $scheduledTaskSubscribersTable
      (subscriber_id, task_id,  processed, created_at)
      SELECT DISTINCT ns.subscriber_id as subscriber_id, :taskId as task_id, 0 as processed, :now as created_at
      FROM $newsletterStatsTable as ns
      JOIN $subscribersTable s ON
        ns.subscriber_id = s.id
        AND s.deleted_at is NULL
        AND s.status = :subscribed
        AND GREATEST(COALESCE(s.created_at, '0'), COALESCE(s.last_subscribed_at, '0'), COALESCE(s.last_engagement_at, '0')) < :thresholdDate
      JOIN $subscriberSegmentTable as ss ON ns.subscriber_id = ss.subscriber_id
        AND ss.segment_id = :segmentId
        AND ss.status = :subscribed
      WHERE ns.sent_at > :thresholdDate
        AND ns.sent_at < :upperThresholdDate
        AND ns.subscriber_id NOT IN (
          SELECT DISTINCT subscriber_id as id FROM $newsletterStatsTable WHERE newsletter_id = :newsletterId AND sent_at > :thresholdDate
        );
    ";

    $statement = $this->entityManager->getConnection()->prepare($query);
    $statement->bindParam('now', $nowSql, ParameterType::STRING);
    $statement->bindParam('taskId', $taskId, ParameterType::INTEGER);
    $statement->bindParam('subscribed', $subscribedStatus, ParameterType::STRING);
    $statement->bindParam('thresholdDate', $thresholdDateSql, ParameterType::STRING);
    $statement->bindParam('upperThresholdDate', $upperThresholdDate, ParameterType::STRING);
    $statement->bindParam('newsletterId', $newsletterId, ParameterType::INTEGER);
    $statement->bindParam('segmentId', $segmentId, ParameterType::INTEGER);

    $statement->executeQuery();
    return (int)$statement->rowCount();
  }
}
