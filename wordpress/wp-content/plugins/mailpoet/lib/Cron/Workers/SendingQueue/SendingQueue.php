<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronHelper;
use MailPoet\Cron\Workers\Bounce;
use MailPoet\Cron\Workers\SendingQueue\Tasks\Links;
use MailPoet\Cron\Workers\SendingQueue\Tasks\Mailer as MailerTask;
use MailPoet\Cron\Workers\SendingQueue\Tasks\Newsletter as NewsletterTask;
use MailPoet\Cron\Workers\StatsNotifications\Scheduler as StatsNotificationsScheduler;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Mailer\MailerLog;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Segments\SubscribersFinder;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Statistics\StatisticsNewslettersRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Tasks\Subscribers\BatchIterator;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use Throwable;

class SendingQueue {
  /** @var MailerTask */
  public $mailerTask;

  /** @var NewsletterTask  */
  public $newsletterTask;

  const TASK_TYPE = 'sending';
  const TASK_BATCH_SIZE = 5;
  const EMAIL_WITH_INVALID_SEGMENT_OPTION = 'mailpoet_email_with_invalid_segment';

  /** @var StatsNotificationsScheduler */
  public $statsNotificationsScheduler;

  /** @var SendingErrorHandler */
  private $errorHandler;

  /** @var SendingThrottlingHandler */
  private $throttlingHandler;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var CronHelper */
  private $cronHelper;

  /** @var SubscribersFinder */
  private $subscribersFinder;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var WPFunctions */
  private $wp;

  /** @var Links */
  private $links;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var ScheduledTaskSubscribersRepository */
  private $scheduledTaskSubscribersRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /*** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var EntityManager */
  private $entityManager;

  /** @var StatisticsNewslettersRepository */
  private $statisticsNewslettersRepository;

  /** @var AuthorizedEmailsController */
  private $authorizedEmailsController;

  public function __construct(
    SendingErrorHandler $errorHandler,
    SendingThrottlingHandler $throttlingHandler,
    StatsNotificationsScheduler $statsNotificationsScheduler,
    LoggerFactory $loggerFactory,
    CronHelper $cronHelper,
    SubscribersFinder $subscriberFinder,
    SegmentsRepository $segmentsRepository,
    WPFunctions $wp,
    Links $links,
    ScheduledTasksRepository $scheduledTasksRepository,
    ScheduledTaskSubscribersRepository $scheduledTaskSubscribersRepository,
    MailerTask $mailerTask,
    SubscribersRepository $subscribersRepository,
    SendingQueuesRepository $sendingQueuesRepository,
    EntityManager $entityManager,
    StatisticsNewslettersRepository $statisticsNewslettersRepository,
    AuthorizedEmailsController $authorizedEmailsController,
    $newsletterTask = false
  ) {
    $this->errorHandler = $errorHandler;
    $this->throttlingHandler = $throttlingHandler;
    $this->statsNotificationsScheduler = $statsNotificationsScheduler;
    $this->subscribersFinder = $subscriberFinder;
    $this->mailerTask = $mailerTask;
    $this->newsletterTask = ($newsletterTask) ? $newsletterTask : new NewsletterTask();
    $this->segmentsRepository = $segmentsRepository;
    $this->mailerMetaInfo = new MetaInfo;
    $this->wp = $wp;
    $this->loggerFactory = $loggerFactory;
    $this->cronHelper = $cronHelper;
    $this->links = $links;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->scheduledTaskSubscribersRepository = $scheduledTaskSubscribersRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->entityManager = $entityManager;
    $this->statisticsNewslettersRepository = $statisticsNewslettersRepository;
    $this->authorizedEmailsController = $authorizedEmailsController;
  }

  public function process($timer = false) {
    $timer = $timer ?: microtime(true);
    $this->enforceSendingAndExecutionLimits($timer);
    foreach ($this->scheduledTasksRepository->findRunningSendingTasks(self::TASK_BATCH_SIZE) as $task) {
      $queue = $task->getSendingQueue();
      if (!$queue) {
        continue;
      }

      if ($task->getInProgress()) {
        if ($this->isTimeout($task)) {
          $this->stopProgress($task);
        } else {
          continue;
        }
      }


      $this->startProgress($task);

      try {
        $this->scheduledTasksRepository->touchAllByIds([$task->getId()]);
        $this->processSending($task, (int)$timer);
      } catch (\Exception $e) {
        $this->stopProgress($task);
        throw $e;
      }

      $this->stopProgress($task);
    }
  }

  private function processSending(ScheduledTaskEntity $task, int $timer): void {
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
      'sending queue processing',
      ['task_id' => $task->getId()]
    );

    $this->deleteTaskIfNewsletterDoesNotExist($task);

    $queue = $task->getSendingQueue();
    $newsletter = $this->newsletterTask->getNewsletterFromQueue($task);
    if (!$queue || !$newsletter) {
      return;
    }

    // pre-process newsletter (render, replace shortcodes/links, etc.)
    $newsletter = $this->newsletterTask->preProcessNewsletter($newsletter, $task);

    // During pre-processing we may find that the newsletter can't be sent and we delete it including all associated entities
    // E.g. post notification history newsletter when there are no posts to send
    if (!$newsletter) {
      return;
    }

    $isTransactional = in_array($newsletter->getType(), [
      NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL,
      NewsletterEntity::TYPE_WC_TRANSACTIONAL_EMAIL,
    ]);

    // configure mailer
    $this->mailerTask->configureMailer($newsletter);
    // get newsletter segments
    $newsletterSegmentsIds = $newsletter->getSegmentIds();
    $segmentIdsToCheck = $newsletterSegmentsIds;
    $filterSegmentId = $newsletter->getFilterSegmentId();

    if (is_int($filterSegmentId)) {
      $segmentIdsToCheck[] = $filterSegmentId;
    }

    // Pause task in case some of related segments was deleted or trashed
    if ($newsletterSegmentsIds && !$this->checkDeletedSegments($segmentIdsToCheck)) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
        'pause task in sending queue due deleted or trashed segment',
        ['task_id' => $task->getId()]
      );
      $task->setStatus(ScheduledTaskEntity::STATUS_PAUSED);
      $this->scheduledTasksRepository->flush();
      $this->wp->setTransient(self::EMAIL_WITH_INVALID_SEGMENT_OPTION, $newsletter->getSubject());
      return;
    }

    // Pause task if sender domain requirements are not met
    if (!$this->authorizedEmailsController->isSenderAddressValid($newsletter, 'sending')) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
        'pause task in sending queue due to sender domain requirements',
        ['task_id' => $task->getId()]
      );
      $task->setStatus(ScheduledTaskEntity::STATUS_PAUSED);
      $this->scheduledTasksRepository->flush();
      return;
    }

    // get subscribers
    $subscriberBatches = new BatchIterator($task->getId(), $this->getBatchSize());
    if ($subscriberBatches->count() === 0) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
        'no subscribers to process',
        ['task_id' => $task->getId()]
      );
      $this->scheduledTasksRepository->invalidateTask($task);
      return;
    }
    /** @var int[] $subscribersToProcessIds - it's required for PHPStan */
    foreach ($subscriberBatches as $subscribersToProcessIds) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
        'subscriber batch processing',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId(), 'subscriber_batch_count' => count($subscribersToProcessIds)]
      );
      if (!empty($newsletterSegmentsIds[0])) {
        // Check that subscribers are in segments
        try {
          $foundSubscribersIds = $this->subscribersFinder->findSubscribersInSegments($subscribersToProcessIds, $newsletterSegmentsIds, $filterSegmentId);
        } catch (InvalidStateException $exception) {
          $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
            'paused task in sending queue due to problem finding subscribers: ' . $exception->getMessage(),
            ['task_id' => $task->getId()]
          );
          $task->setStatus(ScheduledTaskEntity::STATUS_PAUSED);
          $this->scheduledTasksRepository->flush();
          return;
        }
        $foundSubscribers = empty($foundSubscribersIds) ? [] : $this->subscribersRepository->findBy(['id' => $foundSubscribersIds, 'deletedAt' => null]);
      } else {
        // No segments = Welcome emails or some Automatic emails.
        // Welcome emails or some Automatic emails use segments only for scheduling and store them as a newsletter option
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('s')
          ->from(SubscriberEntity::class, 's')
          ->where('s.id IN (:subscriberIds)')
          ->setParameter('subscriberIds', $subscribersToProcessIds)
          ->andWhere('s.deletedAt IS NULL');

        if ($newsletter->getType() === NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL) {
          $queryBuilder->andWhere('s.status != :bouncedStatus')
            ->setParameter('bouncedStatus', SubscriberEntity::STATUS_BOUNCED);
        } else {
          $queryBuilder->andWhere('s.status = :subscribedStatus')
            ->setParameter('subscribedStatus', SubscriberEntity::STATUS_SUBSCRIBED);
        }

        $foundSubscribers = $queryBuilder->getQuery()->getResult();
        $foundSubscribersIds = array_map(function(SubscriberEntity $subscriber) {
          return $subscriber->getId();
        }, $foundSubscribers);
      }

      // if some subscribers weren't found, remove them from the processing list
      if (count($foundSubscribersIds) !== count($subscribersToProcessIds)) {
        $subscribersToRemove = array_diff(
          $subscribersToProcessIds,
          $foundSubscribersIds
        );

        $this->scheduledTaskSubscribersRepository->deleteByScheduledTaskAndSubscriberIds($task, $subscribersToRemove);
        $this->sendingQueuesRepository->updateCounts($queue);

        if (!$queue->getCountToProcess()) {
          $this->newsletterTask->markNewsletterAsSent($newsletter);
          continue;
        }
        // if there aren't any subscribers to process in batch (e.g. all unsubscribed or were deleted) continue with next batch
        if (count($foundSubscribersIds) === 0) {
          continue;
        }
      }
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
        'before queue chunk processing',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId(), 'found_subscribers_count' => count($foundSubscribers)]
      );

      // reschedule bounce task to run sooner, if needed
      $this->reScheduleBounceTask();

      // Check task has not been paused before continue processing
      // This is needed because the task can be paused in the middle of the batch processing,
      // for example on API error ERROR_MESSAGE_BULK_EMAIL_FORBIDDEN
      if ($task->getStatus() === ScheduledTaskEntity::STATUS_PAUSED) {
        return;
      }

      if ($newsletter->getStatus() !== NewsletterEntity::STATUS_CORRUPT) {
        $this->processQueue(
          $task,
          $newsletter,
          $foundSubscribers,
          $timer
        );
        if (!$isTransactional) {
          $this->entityManager->wrapInTransaction(function() use ($foundSubscribersIds) {
            $now = Carbon::createFromTimestamp((int)current_time('timestamp'));
            $this->subscribersRepository->bulkUpdateLastSendingAt($foundSubscribersIds, $now);
            // We're nullifying this value so these subscribers' engagement score will be recalculated the next time the cron runs
            $this->subscribersRepository->bulkUpdateEngagementScoreUpdatedAt($foundSubscribersIds, null);
          });
        }
        $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
          'after queue chunk processing',
          ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
        );
        if ($task->getStatus() === ScheduledTaskEntity::STATUS_COMPLETED) {
          $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
            'completed newsletter sending',
            ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
          );
          $this->newsletterTask->markNewsletterAsSent($newsletter);
          $this->statsNotificationsScheduler->schedule($newsletter);
        }
        $this->enforceSendingAndExecutionLimits($timer);
      } else {
        $this->sendingQueuesRepository->pause($queue);
        $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->error(
          'Can\'t send corrupt newsletter',
          ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
        );
      }
    }
  }

  public function getBatchSize(): int {
    return $this->throttlingHandler->getBatchSize();
  }

  /**
   * @param SubscriberEntity[] $subscribers
   */
  public function processQueue(ScheduledTaskEntity $task, NewsletterEntity $newsletter, array $subscribers, $timer) {
    // determine if processing is done in bulk or individually
    $processingMethod = $this->mailerTask->getProcessingMethod();
    $preparedNewsletters = [];
    $preparedSubscribers = [];
    $preparedSubscribersIds = [];
    $unsubscribeUrls = [];
    $statistics = [];
    $metas = [];
    $oneClickUnsubscribeUrls = [];
    $sendingQueueEntity = $task->getSendingQueue();
    if (!$sendingQueueEntity) {
      return;
    }

    $sendingQueueMeta = $sendingQueueEntity->getMeta() ?? [];
    $campaignId = $sendingQueueMeta['campaignId'] ?? null;

    foreach ($subscribers as $subscriber) {
      // render shortcodes and replace subscriber data in tracked links
      $preparedNewsletters[] =
        $this->newsletterTask->prepareNewsletterForSending(
          $newsletter,
          $subscriber,
          $sendingQueueEntity
        );
      // format subscriber name/address according to mailer settings
      $preparedSubscribers[] = $this->mailerTask->prepareSubscriberForSending(
        $subscriber
      );
      $preparedSubscribersIds[] = $subscriber->getId();
      // create personalized instant unsubsribe link
      $unsubscribeUrls[] = $this->links->getUnsubscribeUrl($sendingQueueEntity->getId(), $subscriber);
      $oneClickUnsubscribeUrls[] = $this->links->getOneClickUnsubscribeUrl($sendingQueueEntity->getId(), $subscriber);

      $metasForSubscriber = $this->mailerMetaInfo->getNewsletterMetaInfo($newsletter, $subscriber);
      if ($campaignId) {
        $metasForSubscriber['campaign_id'] = $campaignId;
      }
      $metas[] = $metasForSubscriber;

      // keep track of values for statistics purposes
      $statistics[] = [
        'newsletter_id' => $newsletter->getId(),
        'subscriber_id' => $subscriber->getId(),
        'queue_id' => $sendingQueueEntity->getId(),
      ];
      if ($processingMethod === 'individual') {
        $this->sendNewsletter(
          $task,
          $preparedSubscribersIds[0],
          $preparedNewsletters[0],
          $preparedSubscribers[0],
          $statistics[0],
          $timer,
          [
            'unsubscribe_url' => $unsubscribeUrls[0],
            'meta' => $metas[0],
            'one_click_unsubscribe' => $oneClickUnsubscribeUrls,
          ]
        );
        $preparedNewsletters = [];
        $preparedSubscribers = [];
        $preparedSubscribersIds = [];
        $unsubscribeUrls = [];
        $oneClickUnsubscribeUrls = [];
        $statistics = [];
        $metas = [];
      }
    }
    if ($processingMethod === 'bulk') {
      $this->sendNewsletters(
        $task,
        $preparedSubscribersIds,
        $preparedNewsletters,
        $preparedSubscribers,
        $statistics,
        $timer,
        [
          'unsubscribe_url' => $unsubscribeUrls,
          'meta' => $metas,
          'one_click_unsubscribe' => $oneClickUnsubscribeUrls,
        ]
      );
    }
  }

  public function sendNewsletter(
    ScheduledTaskEntity $task, $preparedSubscriberId, $preparedNewsletter,
    $preparedSubscriber, $statistics, $timer, $extraParams = []
  ) {
    // send newsletter
    $sendResult = $this->mailerTask->send(
      $preparedNewsletter,
      $preparedSubscriber,
      $extraParams
    );
    $this->processSendResult(
      $task,
      $sendResult,
      [$preparedSubscriber],
      [$preparedSubscriberId],
      [$statistics],
      $timer
    );
  }

  public function sendNewsletters(
    ScheduledTaskEntity $task, $preparedSubscribersIds, $preparedNewsletters,
    $preparedSubscribers, $statistics, $timer, $extraParams = []
  ) {
    // send newsletters
    $sendResult = $this->mailerTask->sendBulk(
      $preparedNewsletters,
      $preparedSubscribers,
      $extraParams
    );
    $this->processSendResult(
      $task,
      $sendResult,
      $preparedSubscribers,
      $preparedSubscribersIds,
      $statistics,
      $timer
    );
  }

  /**
   * Checks whether some of segments was deleted or trashed
   * @param int[] $segmentIds
   */
  private function checkDeletedSegments(array $segmentIds): bool {
    if (count($segmentIds) === 0) {
      return true;
    }
    $segmentIds = array_unique($segmentIds);
    $segments = $this->segmentsRepository->findBy(['id' => $segmentIds]);
    // Some segment was deleted from DB
    if (count($segmentIds) > count($segments)) {
      return false;
    }
    foreach ($segments as $segment) {
      if ($segment->getDeletedAt() !== null) {
        return false;
      }
    }
    return true;
  }

  private function processSendResult(
    ScheduledTaskEntity $task,
    $sendResult,
    array $preparedSubscribers,
    array $preparedSubscribersIds,
    array $statistics,
    $timer
  ) {
    // log error message and schedule retry/pause sending
    if ($sendResult['response'] === false) {
      $error = $sendResult['error'];
      $this->errorHandler->processError($error, $task, $preparedSubscribersIds, $preparedSubscribers);
    } else {
      $queue = $task->getSendingQueue();
      if (!$queue) {
        return;
      }
      try {
        $this->scheduledTaskSubscribersRepository->updateProcessedSubscribers($task, $preparedSubscribersIds);
        $this->sendingQueuesRepository->updateCounts($queue);
      } catch (Throwable $e) {
        MailerLog::processError(
          'processed_list_update',
          sprintf('QUEUE-%d-PROCESSED-LIST-UPDATE', $queue->getId()),
          null,
          true
        );
      }
    }

    // log statistics
    $this->statisticsNewslettersRepository->createMultiple($statistics);

    // update the sent count
    $this->mailerTask->updateSentCount();

    // enforce execution limits if queue is still being processed
    if ($task->getStatus() !== ScheduledTaskEntity::STATUS_COMPLETED) {
      $this->enforceSendingAndExecutionLimits($timer);
    }

    // trigger automation email sent hook for automation emails
    if (
      $task->getStatus() === ScheduledTaskEntity::STATUS_COMPLETED
      && isset($task->getMeta()['automation'])
    ) {
      try {
        $this->wp->doAction('mailpoet_automation_email_sent', $task->getMeta()['automation']);
      } catch (Throwable $e) {
        $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->error(
          'Error while executing "mailpoet_automation_email_sent action" hook',
          ['task_id' => $task->getId(), 'error' => $e->getMessage()]
        );
      }
    }

    $this->throttlingHandler->processSuccess();
  }

  public function enforceSendingAndExecutionLimits($timer) {
    // abort if execution limit is reached
    $this->cronHelper->enforceExecutionLimit($timer);
    // abort if sending limit has been reached
    MailerLog::enforceExecutionRequirements();
  }

  private function reScheduleBounceTask() {
    $bounceTasks = $this->scheduledTasksRepository->findFutureScheduledByType(Bounce::TASK_TYPE);
    if (count($bounceTasks)) {
      $bounceTask = reset($bounceTasks);
      if (Carbon::createFromTimestamp((int)current_time('timestamp'))->addHours(42)->lessThan($bounceTask->getScheduledAt())) {
        $randomOffset = rand(-6 * 60 * 60, 6 * 60 * 60);
        $bounceTask->setScheduledAt(Carbon::createFromTimestamp((int)current_time('timestamp'))->addSeconds((36 * 60 * 60) + $randomOffset));
        $this->scheduledTasksRepository->persist($bounceTask);
        $this->scheduledTasksRepository->flush();
      }
    }
  }

  private function startProgress(ScheduledTaskEntity $task): void {
    $task->setInProgress(true);
    $this->scheduledTasksRepository->flush();
  }

  private function stopProgress(ScheduledTaskEntity $task): void {
    // if task is not managed by entity manager, it's already deleted and detached
    // it can be deleted in self::processSending method
    if (!$this->entityManager->contains($task)) {
      return;
    }
    $task->setInProgress(false);
    $this->scheduledTasksRepository->flush();
  }

  private function isTimeout(ScheduledTaskEntity $task): bool {
    $currentTime = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $updatedAt = new Carbon($task->getUpdatedAt());
    if ($updatedAt->diffInSeconds($currentTime, false) > $this->getExecutionLimit()) {
      return true;
    }

    return false;
  }

  private function getExecutionLimit(): int {
    return $this->cronHelper->getDaemonExecutionLimit() * 3;
  }

  private function deleteTaskIfNewsletterDoesNotExist(ScheduledTaskEntity $task) {
    $queue = $task->getSendingQueue();
    $newsletter = $queue ? $queue->getNewsletter() : null;
    if ($newsletter !== null) {
      return;
    }
    $this->deleteTask($task);
  }

  private function deleteTask(ScheduledTaskEntity $task) {
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
      'delete task in sending queue',
      ['task_id' => $task->getId()]
    );

    $queue = $task->getSendingQueue();
    if ($queue) {
      $this->sendingQueuesRepository->remove($queue);
    }
    $this->scheduledTaskSubscribersRepository->deleteByScheduledTask($task);
    $this->scheduledTasksRepository->remove($task);
    $this->scheduledTasksRepository->flush();
  }
}
