<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronHelper;
use MailPoet\Cron\CronWorkerScheduler;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterSegmentEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Scheduler\PostNotificationScheduler;
use MailPoet\Newsletter\Scheduler\Scheduler as NewsletterScheduler;
use MailPoet\Newsletter\Scheduler\WelcomeScheduler;
use MailPoet\Newsletter\Segment\NewsletterSegmentRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Segments\SubscribersFinder;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityNotFoundException;

class Scheduler {
  const TASK_BATCH_SIZE = 5;

  /** @var SubscribersFinder */
  private $subscribersFinder;

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var CronHelper */
  private $cronHelper;

  /** @var CronWorkerScheduler */
  private $cronWorkerScheduler;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var ScheduledTaskSubscribersRepository */
  private $scheduledTaskSubscribersRepository;

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var NewsletterSegmentRepository */
  private $newsletterSegmentRepository;

  /** @var WPFunctions */
  private $wp;

  /** @var Security */
  private $security;

  /** @var NewsletterScheduler */
  private $scheduler;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    SubscribersFinder $subscribersFinder,
    LoggerFactory $loggerFactory,
    CronHelper $cronHelper,
    CronWorkerScheduler $cronWorkerScheduler,
    ScheduledTasksRepository $scheduledTasksRepository,
    ScheduledTaskSubscribersRepository $scheduledTaskSubscribersRepository,
    SendingQueuesRepository $sendingQueuesRepository,
    NewslettersRepository $newslettersRepository,
    SegmentsRepository $segmentsRepository,
    NewsletterSegmentRepository $newsletterSegmentRepository,
    WPFunctions $wp,
    Security $security,
    NewsletterScheduler $scheduler,
    SubscriberSegmentRepository $subscriberSegmentRepository,
    SubscribersRepository $subscribersRepository
  ) {
    $this->cronHelper = $cronHelper;
    $this->subscribersFinder = $subscribersFinder;
    $this->loggerFactory = $loggerFactory;
    $this->cronWorkerScheduler = $cronWorkerScheduler;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->scheduledTaskSubscribersRepository = $scheduledTaskSubscribersRepository;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->segmentsRepository = $segmentsRepository;
    $this->newsletterSegmentRepository = $newsletterSegmentRepository;
    $this->wp = $wp;
    $this->security = $security;
    $this->scheduler = $scheduler;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function process($timer = false) {
    $timer = $timer ?: microtime(true);

    // abort if execution limit is reached
    $this->cronHelper->enforceExecutionLimit($timer);

    $scheduledTasks = $this->getScheduledSendingTasks();
    $this->updateTasks($scheduledTasks);
    foreach ($scheduledTasks as $task) {
      $queue = $task->getSendingQueue();
      if (!$queue) {
        $this->deleteByTask($task);
        continue;
      }

      $newsletter = $queue->getNewsletter();
      try {
        if (!$newsletter instanceof NewsletterEntity || $newsletter->getDeletedAt() !== null) {
          $this->deleteByTask($task);
        } elseif ($newsletter->getStatus() !== NewsletterEntity::STATUS_ACTIVE && $newsletter->getStatus() !== NewsletterEntity::STATUS_SCHEDULED) {
          continue;
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_WELCOME) {
          $this->processWelcomeNewsletter($newsletter, $task);
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_NOTIFICATION) {
          $this->processPostNotificationNewsletter($newsletter, $task);
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_STANDARD) {
          $this->processScheduledStandardNewsletter($newsletter, $task);
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_AUTOMATIC) {
          $this->processScheduledAutomaticEmail($newsletter, $task);
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_RE_ENGAGEMENT) {
          $this->processReEngagementEmail($task);
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_AUTOMATION) {
          $this->processScheduledAutomationEmail($task);
        } elseif ($newsletter->getType() === NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL) {
          $this->processScheduledTransactionalEmail($task);
        }
      } catch (EntityNotFoundException $e) {
        // Doctrine throws this exception when newsletter doesn't exist but is referenced in a scheduled task.
        // This was added while refactoring this method to use Doctrine instead of Paris. We have to handle this case
        // for the SchedulerTest::testItDeletesQueueDuringProcessingWhenNewsletterNotFound() test. I'm not sure
        // if this problem could happen in production or not.
        $this->deleteByTask($task);
      }
      $this->cronHelper->enforceExecutionLimit($timer);
    }
  }

  public function processWelcomeNewsletter(NewsletterEntity $newsletter, ScheduledTaskEntity $task) {
    $subscribers = $task->getSubscribers();
    if (empty($subscribers[0])) {
      $this->deleteByTask($task);
      return false;
    }
    $subscriberId = (int)$subscribers[0]->getSubscriberId();
    if ($newsletter->getOptionValue('event') === 'segment') {
      if ($this->verifyMailpoetSubscriber($subscriberId, $newsletter, $task) === false) {
        return false;
      }
    } else {
      if ($newsletter->getOptionValue('event') === 'user') {
        if ($this->verifyWPSubscriber($subscriberId, $newsletter, $task) === false) {
          return false;
        }
      }
    }
    $task->setStatus(null);
    $this->scheduledTasksRepository->flush();
    return true;
  }

  public function processPostNotificationNewsletter(NewsletterEntity $newsletter, ScheduledTaskEntity $task) {
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'process post notification in scheduler',
      ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
    );

    // ensure that segments exist
    $segments = $newsletter->getSegmentIds();
    if (empty($segments)) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
        'post notification no segments',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
      );
      $this->deleteQueueOrUpdateNextRunDate($task, $newsletter);
      return false;
    }

    // ensure that subscribers are in segments
    $subscribersCount = $this->subscribersFinder->addSubscribersToTaskFromSegments($task, $segments, $newsletter->getFilterSegmentId());
    if (empty($subscribersCount)) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
        'post notification no subscribers',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId(), 'segment_ids' => $segments]
      );
      $this->deleteQueueOrUpdateNextRunDate($task, $newsletter);
      return false;
    }

    // create a duplicate newsletter that acts as a history record
    try {
      $notificationHistory = $this->createPostNotificationHistory($newsletter);
    } catch (\Exception $exception) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->error(
        'creating post notification history failed',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId(), 'error' => $exception->getMessage()]
      );
      return false;
    }

    // queue newsletter for delivery
    $queue = $task->getSendingQueue();
    if (!$queue) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->error(
        'post notification no queue',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
      );
      return false;
    }
    $queue->setNewsletter($notificationHistory);
    $this->sendingQueuesRepository->updateCounts($queue);
    $task->setStatus(null);
    $this->scheduledTasksRepository->flush();

    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'post notification set status to sending',
      ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
    );
    return true;
  }

  public function processScheduledAutomaticEmail(NewsletterEntity $newsletter, ScheduledTaskEntity $task) {
    if ($newsletter->getOptionValue('sendTo') === 'segment') {
      $segment = $this->segmentsRepository->findOneById($newsletter->getOptionValue('segment'));
      if ($segment instanceof SegmentEntity) {
        $result = $this->subscribersFinder->addSubscribersToTaskFromSegments($task, [(int)$segment->getId()]);

        if (empty($result)) {
          $this->deleteByTask($task);
          return false;
        }
      }
    } else {
      $subscribers = $task->getSubscribers();
      $subscriber = isset($subscribers[0]) ? $subscribers[0]->getSubscriber() : null;
      if (!$subscriber) {
        $this->deleteByTask($task);
        return false;
      }
      if ($this->verifySubscriber($subscriber, $task) === false) {
        return false;
      }
    }

    $task->setStatus(null);
    $this->scheduledTasksRepository->flush();
    return true;
  }

  public function processScheduledAutomationEmail(ScheduledTaskEntity $task): bool {
    $subscribers = $task->getSubscribers();
    $subscriber = isset($subscribers[0]) ? $subscribers[0]->getSubscriber() : null;
    if (!$subscriber) {
      $this->deleteByTask($task);
      return false;
    }
    if (!$this->verifySubscriber($subscriber, $task)) {
      return false;
    }

    $task->setStatus(null);
    $this->scheduledTasksRepository->flush();
    return true;
  }

  public function processScheduledTransactionalEmail(ScheduledTaskEntity $task): bool {
    $subscribers = $task->getSubscribers();
    $subscriber = isset($subscribers[0]) ? $subscribers[0]->getSubscriber() : null;
    if (!$subscriber) {
      $this->deleteByTask($task);
      return false;
    }
    if (!$this->verifySubscriber($subscriber, $task)) {
      $this->deleteByTask($task);
      return false;
    }

    $task->setStatus(null);
    $this->scheduledTasksRepository->flush();
    return true;
  }

  public function processScheduledStandardNewsletter(NewsletterEntity $newsletter, ScheduledTaskEntity $task) {
    $segments = $newsletter->getSegmentIds();
    $this->subscribersFinder->addSubscribersToTaskFromSegments($task, $segments, $newsletter->getFilterSegmentId());

    $task->setStatus(null);
    $queue = $task->getSendingQueue();
    if ($queue) {
      $this->sendingQueuesRepository->updateCounts($queue);
    }
    $newsletter->setStatus(NewsletterEntity::STATUS_SENDING);
    $this->scheduledTasksRepository->flush();
    return true;
  }

  private function processReEngagementEmail(ScheduledTaskEntity $task) {
    $task->setStatus(null);
    $this->scheduledTasksRepository->flush();
    return true;
  }

  public function verifyMailpoetSubscriber(int $subscriberId, NewsletterEntity $newsletter, ScheduledTaskEntity $task): bool {
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);

    // check if subscriber is in proper segment
    $subscriberInSegment = $this->subscriberSegmentRepository->findOneBy(
      [
        'subscriber' => $subscriberId,
        'segment' => $newsletter->getOptionValue('segment'),
        'status' => SubscriberEntity::STATUS_SUBSCRIBED,
      ]
    );
    if (!$subscriber || !$subscriberInSegment) {
      $this->deleteByTask($task);
      return false;
    }
    return $this->verifySubscriber($subscriber, $task);
  }

  public function verifyWPSubscriber(int $subscriberId, NewsletterEntity $newsletter, ScheduledTaskEntity $task): bool {
    // check if user has the proper role
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!$subscriber || $subscriber->isWPUser() === false || is_null($subscriber->getWpUserId())) {
      $this->deleteByTask($task);
      return false;
    }
    $wpUser = get_userdata($subscriber->getWpUserId());
    if ($wpUser === false) {
      $this->deleteByTask($task);
      return false;
    }
    if (
      $newsletter->getOptionValue('role') !== WelcomeScheduler::WORDPRESS_ALL_ROLES
      && !in_array($newsletter->getOptionValue('role'), ((array)$wpUser)['roles'])
    ) {
      $this->deleteByTask($task);
      return false;
    }
    return $this->verifySubscriber($subscriber, $task);
  }

  public function verifySubscriber(SubscriberEntity $subscriber, ScheduledTaskEntity $task): bool {
    $queue = $task->getSendingQueue();
    $newsletter = $queue ? $queue->getNewsletter() : null;
    if ($newsletter && $newsletter->getType() === NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL) {
      return $subscriber->getStatus() !== SubscriberEntity::STATUS_BOUNCED;
    }
    if ($subscriber->getStatus() === SubscriberEntity::STATUS_UNCONFIRMED) {
      // reschedule delivery
      $this->cronWorkerScheduler->rescheduleProgressively($task);
      return false;
    } else if ($subscriber->getStatus() === SubscriberEntity::STATUS_UNSUBSCRIBED) {
      $this->deleteByTask($task);
      return false;
    }
    return true;
  }

  public function deleteQueueOrUpdateNextRunDate(ScheduledTaskEntity $task, NewsletterEntity $newsletter) {
    if ($newsletter->getOptionValue('intervalType') === PostNotificationScheduler::INTERVAL_IMMEDIATELY) {
      $this->deleteByTask($task);
    } else {
      $nextRunDate = $this->scheduler->getNextRunDateTime($newsletter->getOptionValue('schedule'));
      if (!$nextRunDate) {
        $this->deleteByTask($task);
        return;
      }
      $task->setScheduledAt($nextRunDate);
      $this->scheduledTasksRepository->flush();
    }
  }

  public function createPostNotificationHistory(NewsletterEntity $newsletter): NewsletterEntity {
    // clone newsletter
    $notificationHistory = clone $newsletter;
    $notificationHistory->setParent($newsletter);
    $notificationHistory->setType(NewsletterEntity::TYPE_NOTIFICATION_HISTORY);
    $notificationHistory->setStatus(NewsletterEntity::STATUS_SENDING);
    $notificationHistory->setUnsubscribeToken($this->security->generateUnsubscribeTokenByEntity($notificationHistory));

    // reset timestamps
    $createdAt = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $notificationHistory->setCreatedAt($createdAt);
    $notificationHistory->setUpdatedAt($createdAt);
    $notificationHistory->setDeletedAt(null);

    // reset hash
    $notificationHistory->setHash(Security::generateHash());

    $this->newslettersRepository->persist($notificationHistory);
    $this->newslettersRepository->flush();

    // create relationships between notification history and segments
    foreach ($newsletter->getNewsletterSegments() as $newsletterSegment) {
      $segment = $newsletterSegment->getSegment();
      if (!$segment) {
        continue;
      }
      $duplicateSegment = new NewsletterSegmentEntity($notificationHistory, $segment);
      $notificationHistory->getNewsletterSegments()->add($duplicateSegment);
      $this->newsletterSegmentRepository->persist($duplicateSegment);
    }
    $this->newslettersRepository->flush();

    return $notificationHistory;
  }

  /**
   * @param ScheduledTaskEntity[] $scheduledTasks
   */
  private function updateTasks(array $scheduledTasks): void {
    $ids = array_map(function (ScheduledTaskEntity $scheduledTask): ?int {
      return $scheduledTask->getId();
    }, $scheduledTasks);
    $ids = array_filter($ids);
    $this->scheduledTasksRepository->touchAllByIds($ids);
  }

  /**
   * @return ScheduledTaskEntity[]
   */
  public function getScheduledSendingTasks(): array {
    return $this->scheduledTasksRepository->findScheduledSendingTasks(self::TASK_BATCH_SIZE);
  }

  private function deleteByTask(ScheduledTaskEntity $task): void {
    $queue = $task->getSendingQueue();
    if ($queue) {
      $this->sendingQueuesRepository->remove($queue);
    }
    $this->scheduledTaskSubscribersRepository->deleteByScheduledTask($task);
    $this->scheduledTasksRepository->remove($task);
    $this->scheduledTasksRepository->flush();
  }
}
