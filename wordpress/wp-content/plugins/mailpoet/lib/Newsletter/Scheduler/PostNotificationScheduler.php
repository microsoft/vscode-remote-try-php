<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Scheduler;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\SendingQueue;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\NewsletterPostsRepository;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository;
use MailPoet\Newsletter\Options\NewsletterOptionsRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\WP\DateTime;
use MailPoet\WP\Posts;

class PostNotificationScheduler {

  const SECONDS_IN_MINUTE = 60;
  const SECONDS_IN_HOUR = 3600;
  const LAST_WEEKDAY_FORMAT = 'L';
  const INTERVAL_DAILY = 'daily';
  const INTERVAL_IMMEDIATELY = 'immediately';
  const INTERVAL_NTHWEEKDAY = 'nthWeekDay';
  const INTERVAL_WEEKLY = 'weekly';
  const INTERVAL_IMMEDIATE = 'immediate';
  const INTERVAL_MONTHLY = 'monthly';

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterOptionsRepository */
  private $newsletterOptionsRepository;

  /** @var NewsletterOptionFieldsRepository */
  private $newsletterOptionFieldsRepository;

  /** @var NewsletterPostsRepository */
  private $newsletterPostsRepository;

  /** @var Scheduler */
  private $scheduler;

  /*** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /*** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  public function __construct(
    NewslettersRepository $newslettersRepository,
    NewsletterOptionsRepository $newsletterOptionsRepository,
    NewsletterOptionFieldsRepository $newsletterOptionFieldsRepository,
    NewsletterPostsRepository $newsletterPostsRepository,
    Scheduler $scheduler,
    ScheduledTasksRepository $scheduledTasksRepository,
    SendingQueuesRepository $sendingQueuesRepository
  ) {
    $this->loggerFactory = LoggerFactory::getInstance();
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterOptionsRepository = $newsletterOptionsRepository;
    $this->newsletterOptionFieldsRepository = $newsletterOptionFieldsRepository;
    $this->newsletterPostsRepository = $newsletterPostsRepository;
    $this->scheduler = $scheduler;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
  }

  public function transitionHook($newStatus, $oldStatus, $post) {
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'transition post notification hook initiated',
      [
        'post_id' => $post->ID,
        'new_status' => $newStatus,
        'old_status' => $oldStatus,
      ]
    );
    $types = Posts::getTypes();
    if (($newStatus !== 'publish') || $oldStatus === 'publish' || !isset($types[$post->post_type])) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      return;
    }
    $this->schedulePostNotification($post->ID);
  }

  public function schedulePostNotification($postId) {
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'schedule post notification hook',
      ['post_id' => $postId]
    );
    $newsletters = $this->newslettersRepository->findActiveByTypes([NewsletterEntity::TYPE_NOTIFICATION]);
    $this->newslettersRepository->prefetchOptions($newsletters);
    if (!count($newsletters)) {
      return false;
    }
    foreach ($newsletters as $newsletter) {
      $post = $this->newsletterPostsRepository->findOneBy([
        'newsletter' => $newsletter,
        'postId' => $postId,
      ]);
      if ($post === null) {
        $this->createPostNotificationSendingTask($newsletter);
      }
    }
  }

  public function createPostNotificationSendingTask(NewsletterEntity $newsletter): ?ScheduledTaskEntity {
    $notificationHistory = $this->newslettersRepository->findSendingNotificationHistoryWithoutPausedOrInvalidTask($newsletter);
    if (count($notificationHistory) > 0) {
      return null;
    }

    $scheduleOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_SCHEDULE);
    if (!$scheduleOption) {
      return null;
    }
    $nextRunDate = $this->scheduler->getNextRunDateTime($scheduleOption->getValue());
    if (!$nextRunDate) {
      return null;
    }

    // do not schedule duplicate queues for the same time
    $lastQueue = $newsletter->getLatestQueue();
    $task = $lastQueue !== null ? $lastQueue->getTask() : null;
    $scheduledAt = $task !== null ? $task->getScheduledAt() : null;
    if ($scheduledAt && $scheduledAt->format('Y-m-d H:i:s') === $nextRunDate->format('Y-m-d H:i:s')) {
      return null;
    }

    $scheduledTask = new ScheduledTaskEntity();
    $scheduledTask->setType(SendingQueue::TASK_TYPE);
    $scheduledTask->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $scheduledTask->setScheduledAt($nextRunDate);
    $scheduledTask->setPriority(ScheduledTaskEntity::PRIORITY_MEDIUM);
    $this->scheduledTasksRepository->persist($scheduledTask);
    $this->scheduledTasksRepository->flush();

    $sendingQueue = new SendingQueueEntity();
    $sendingQueue->setNewsletter($newsletter);
    $sendingQueue->setTask($scheduledTask);
    $this->sendingQueuesRepository->persist($sendingQueue);
    $this->sendingQueuesRepository->flush();
    $scheduledTask->setSendingQueue($sendingQueue);

    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'schedule post notification',
      [
        'sending_task' => $scheduledTask->getId(),
        'scheduled_at' => $nextRunDate->format(DateTime::DEFAULT_DATE_TIME_FORMAT),
      ]
    );
    return $scheduledTask;
  }

  public function processPostNotificationSchedule(NewsletterEntity $newsletter) {
    $intervalTypeOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_INTERVAL_TYPE);
    $intervalType = $intervalTypeOption ? $intervalTypeOption->getValue() : null;

    $timeOfDayOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_TIME_OF_DAY);
    $hour = $timeOfDayOption ? (int)floor((int)$timeOfDayOption->getValue() / self::SECONDS_IN_HOUR) : null;
    $minute = $timeOfDayOption ? ((int)$timeOfDayOption->getValue() - (int)($hour * self::SECONDS_IN_HOUR)) / self::SECONDS_IN_MINUTE : null;

    $weekDayOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_WEEK_DAY);
    $weekDay = $weekDayOption ? $weekDayOption->getValue() : null;

    $monthDayOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_MONTH_DAY);
    $monthDay = $monthDayOption ? $monthDayOption->getValue() : null;

    $nthWeekDayOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_NTH_WEEK_DAY);
    $nthWeekDay = $nthWeekDayOption ? $nthWeekDayOption->getValue() : null;
    $nthWeekDay = ($nthWeekDay === self::LAST_WEEKDAY_FORMAT) ? $nthWeekDay : '#' . $nthWeekDay;
    switch ($intervalType) {
      case self::INTERVAL_IMMEDIATE:
      case self::INTERVAL_DAILY:
        $schedule = sprintf('%s %s * * *', $minute, $hour);
        break;
      case self::INTERVAL_WEEKLY:
        $schedule = sprintf('%s %s * * %s', $minute, $hour, $weekDay);
        break;
      case self::INTERVAL_NTHWEEKDAY:
        $schedule = sprintf('%s %s ? * %s%s', $minute, $hour, $weekDay, $nthWeekDay);
        break;
      case self::INTERVAL_MONTHLY:
        $schedule = sprintf('%s %s %s * *', $minute, $hour, $monthDay);
        break;
      case self::INTERVAL_IMMEDIATELY:
      default:
        $schedule = '* * * * *';
        break;
    }
    $optionField = $this->newsletterOptionFieldsRepository->findOneBy([
      'name' => NewsletterOptionFieldEntity::NAME_SCHEDULE,
    ]);
    if (!$optionField instanceof NewsletterOptionFieldEntity) {
      throw new \Exception('NewsletterOptionField for schedule doesn’t exist.');
    }
    $scheduleOption = $newsletter->getOption(NewsletterOptionFieldEntity::NAME_SCHEDULE);
    if ($scheduleOption === null) {
      $scheduleOption = new NewsletterOptionEntity($newsletter, $optionField);
      $newsletter->getOptions()->add($scheduleOption);
    }
    $scheduleOption->setValue($schedule);
    $this->newsletterOptionsRepository->persist($scheduleOption);
    $this->newsletterOptionsRepository->flush();
    return $scheduleOption->getValue();
  }
}
