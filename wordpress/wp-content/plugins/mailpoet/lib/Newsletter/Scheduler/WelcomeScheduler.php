<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Scheduler;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\SendingQueue;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class WelcomeScheduler {

  const WORDPRESS_ALL_ROLES = 'mailpoet_all';

  /** @var EntityManager */
  private $entityManager;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var Scheduler  */
  private $scheduler;

  public function __construct(
    EntityManager $entityManager,
    SubscribersRepository $subscribersRepository,
    SegmentsRepository $segmentsRepository,
    NewslettersRepository $newslettersRepository,
    ScheduledTasksRepository $scheduledTasksRepository,
    Scheduler $scheduler
  ) {
    $this->entityManager = $entityManager;
    $this->subscribersRepository = $subscribersRepository;
    $this->segmentsRepository = $segmentsRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->scheduler = $scheduler;
  }

  public function scheduleSubscriberWelcomeNotification($subscriberId, $segments): void {
    $newsletters = $this->newslettersRepository->findActiveByTypes([NewsletterEntity::TYPE_WELCOME]);
    foreach ($newsletters as $newsletter) {
      if (
        $newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_EVENT) === 'segment' &&
        in_array($newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_SEGMENT), $segments)
      ) {
        $this->createWelcomeNotificationSendingTask($newsletter, $subscriberId);
      }
    }
  }

  public function scheduleWPUserWelcomeNotification(
    $subscriberId,
    $wpUser,
    $oldUserData = false
  ) {
    $newsletters = $this->newslettersRepository->findActiveByTypes([NewsletterEntity::TYPE_WELCOME]);
    if (empty($newsletters)) return false;
    foreach ($newsletters as $newsletter) {
      if ($newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_EVENT) !== 'user') {
        continue;
      }
      $newsletterRole = $newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_ROLE);
      if (!empty($oldUserData['roles'])) {
        // do not schedule welcome newsletter if roles have not changed
        $oldRole = $oldUserData['roles'];
        $newRole = $wpUser['roles'];
        if (
          $newsletterRole === self::WORDPRESS_ALL_ROLES ||
          !array_diff($newRole, $oldRole)
        ) {
          continue;
        }
      }
      if (
        $newsletterRole === self::WORDPRESS_ALL_ROLES ||
        in_array($newsletterRole, $wpUser['roles'])
      ) {
        $this->createWelcomeNotificationSendingTask($newsletter, $subscriberId);
      }
    }
  }

  public function createWelcomeNotificationSendingTask(NewsletterEntity $newsletter, $subscriberId): void {
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!($subscriber instanceof SubscriberEntity) || $subscriber->getDeletedAt() !== null) {
      return;
    }
    if ($newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_EVENT) === 'segment') {
      $segment = $this->segmentsRepository->findOneById((int)$newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_SEGMENT));
      if ((!$segment instanceof SegmentEntity) || $segment->getDeletedAt() !== null) {
        return;
      }
    }
    if ($newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_EVENT) === 'user') {
      $segment = $this->segmentsRepository->getWPUsersSegment();
      if ((!$segment instanceof SegmentEntity) || $segment->getDeletedAt() !== null) {
        return;
      }
    }
    $previouslyScheduledNotification = $this->scheduledTasksRepository->findByNewsletterAndSubscriberId($newsletter, $subscriberId);
    if (!empty($previouslyScheduledNotification)) {
      return;
    }

    // task
    $task = new ScheduledTaskEntity();
    $task->setType(SendingQueue::TASK_TYPE);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $task->setPriority(ScheduledTaskEntity::PRIORITY_HIGH);
    $task->setScheduledAt($this->scheduler->getScheduledTimeWithDelay(
      $newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_AFTER_TIME_TYPE),
      $newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_AFTER_TIME_NUMBER)
    ));
    $this->entityManager->persist($task);

    // queue
    $queue = new SendingQueueEntity();
    $queue->setTask($task);
    $queue->setNewsletter($newsletter);
    // Because we changed the way how to updateCounts after sending we need to set initial counts
    $queue->setCountTotal(1);
    $queue->setCountToProcess(1);

    $task->setSendingQueue($queue);
    $this->entityManager->persist($queue);

    // task subscriber
    $taskSubscriber = new ScheduledTaskSubscriberEntity($task, $subscriber);
    $task->getSubscribers()->add($taskSubscriber);
    $this->entityManager->persist($taskSubscriber);

    $this->entityManager->flush();
  }
}
