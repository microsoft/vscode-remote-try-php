<?php declare(strict_types = 1);

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\Cron\Workers\StatsNotifications\StatsNotificationsRepository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatsNotificationEntity;
use MailPoet\Newsletter\Options\NewsletterOptionsRepository;
use MailPoet\Newsletter\Segment\NewsletterSegmentRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Statistics\StatisticsClicksRepository;
use MailPoet\Statistics\StatisticsNewslettersRepository;
use MailPoet\Statistics\StatisticsOpensRepository;
use MailPoet\Statistics\StatisticsWooCommercePurchasesRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use Throwable;

class NewsletterDeleteController {
  private EntityManager $entityManager;
  private NewslettersRepository $newslettersRepository;
  private NewsletterLinkRepository $newsletterLinkRepository;
  private NewsletterOptionsRepository $newsletterOptionsRepository;
  private NewsletterPostsRepository $newsletterPostsRepository;
  private NewsletterSegmentRepository $newsletterSegmentRepository;
  private ScheduledTasksRepository $scheduledTasksRepository;
  private ScheduledTaskSubscribersRepository $scheduledTaskSubscribersRepository;
  private SendingQueuesRepository $sendingQueuesRepository;
  private StatisticsClicksRepository $statisticsClicksRepository;
  private StatisticsNewslettersRepository $statisticsNewslettersRepository;
  private StatisticsOpensRepository $statisticsOpensRepository;
  private StatisticsWooCommercePurchasesRepository $statisticsWooCommercePurchasesRepository;
  private StatsNotificationsRepository $statsNotificationsRepository;
  private WPFunctions $wp;

  public function __construct(
    EntityManager $entityManager,
    NewslettersRepository $newslettersRepository,
    NewsletterLinkRepository $newsletterLinkRepository,
    NewsletterOptionsRepository $newsletterOptionsRepository,
    NewsletterPostsRepository $newsletterPostsRepository,
    NewsletterSegmentRepository $newsletterSegmentRepository,
    ScheduledTasksRepository $scheduledTasksRepository,
    ScheduledTaskSubscribersRepository $scheduledTaskSubscribersRepository,
    SendingQueuesRepository $sendingQueuesRepository,
    StatisticsClicksRepository $statisticsClicksRepository,
    StatisticsNewslettersRepository $statisticsNewslettersRepository,
    StatisticsOpensRepository $statisticsOpensRepository,
    StatisticsWooCommercePurchasesRepository $statisticsWooCommercePurchasesRepository,
    StatsNotificationsRepository $statsNotificationsRepository,
    WPFunctions $wp
  ) {
    $this->entityManager = $entityManager;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
    $this->newsletterOptionsRepository = $newsletterOptionsRepository;
    $this->newsletterPostsRepository = $newsletterPostsRepository;
    $this->newsletterSegmentRepository = $newsletterSegmentRepository;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->scheduledTaskSubscribersRepository = $scheduledTaskSubscribersRepository;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->statisticsClicksRepository = $statisticsClicksRepository;
    $this->statisticsNewslettersRepository = $statisticsNewslettersRepository;
    $this->statisticsOpensRepository = $statisticsOpensRepository;
    $this->statisticsWooCommercePurchasesRepository = $statisticsWooCommercePurchasesRepository;
    $this->statsNotificationsRepository = $statsNotificationsRepository;
    $this->wp = $wp;
  }

  /** @param int[] $ids */
  public function bulkDelete(array $ids): int {
    if (!$ids) {
      return 0;
    }

    // Fetch children ids for deleting
    $childrenIds = $this->newslettersRepository->fetchChildrenIds($ids);
    $ids = array_merge($ids, $childrenIds);

    $this->entityManager->beginTransaction();
    try {
      // Delete statistics data
      $this->statisticsNewslettersRepository->deleteByNewsletterIds($ids);
      $this->statisticsOpensRepository->deleteByNewsletterIds($ids);
      $this->statisticsClicksRepository->deleteByNewsletterIds($ids);

      // Update WooCommerce statistics and remove newsletter and click id
      $this->statisticsWooCommercePurchasesRepository->removeNewsletterDataByNewsletterIds($ids);

      // Delete newsletter posts, options, links, and segments
      $this->newsletterPostsRepository->deleteByNewsletterIds($ids);
      $this->newsletterOptionsRepository->deleteByNewsletterIds($ids);
      $this->newsletterLinkRepository->deleteByNewsletterIds($ids);
      $this->newsletterSegmentRepository->deleteByNewsletterIds($ids);

      // Delete stats notifications and related tasks
      /** @var string[] $taskIds */
      $taskIds = $this->entityManager->createQueryBuilder()
        ->select('IDENTITY(sn.task)')
        ->from(StatsNotificationEntity::class, 'sn')
        ->where('sn.newsletter IN (:ids)')
        ->setParameter('ids', $ids)
        ->getQuery()
        ->getSingleColumnResult();
      $taskIds = array_map('intval', $taskIds);

      $this->scheduledTasksRepository->deleteByIds($taskIds);
      $this->statsNotificationsRepository->deleteByNewsletterIds($ids);

      // Delete scheduled task subscribers, scheduled tasks, and sending queues
      /** @var string[] $taskIds */
      $taskIds = $this->entityManager->createQueryBuilder()
        ->select('IDENTITY(q.task)')
        ->from(SendingQueueEntity::class, 'q')
        ->where('q.newsletter IN (:ids)')
        ->setParameter('ids', $ids)
        ->getQuery()
        ->getSingleColumnResult();
      $taskIds = array_map('intval', $taskIds);

      $this->scheduledTaskSubscribersRepository->deleteByTaskIds($taskIds);
      $this->scheduledTasksRepository->deleteByIds($taskIds);
      $this->sendingQueuesRepository->deleteByNewsletterIds($ids);

      // Fetch WP Posts IDs and delete them
      /** @var string[] $wpPostIds */
      $wpPostIds = $this->entityManager->createQueryBuilder()
        ->select('IDENTITY(n.wpPost) AS id')
        ->from(NewsletterEntity::class, 'n')
        ->where('n.id IN (:ids)')
        ->andWhere('n.wpPost IS NOT NULL')
        ->setParameter('ids', $ids)
        ->getQuery()
        ->getSingleColumnResult();
      $wpPostIds = array_map('intval', $wpPostIds);

      foreach ($wpPostIds as $wpPostId) {
        $this->wp->wpDeletePost($wpPostId, true);
      }

      // Delete newsletter entities
      $this->newslettersRepository->deleteByIds($ids);

      $this->entityManager->commit();
    } catch (Throwable $e) {
      $this->entityManager->rollback();
      throw $e;
    }

    return count($ids);
  }
}
