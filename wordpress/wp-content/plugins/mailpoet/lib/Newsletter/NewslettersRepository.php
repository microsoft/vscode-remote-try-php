<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart;
use MailPoet\AutomaticEmails\WooCommerce\Events\FirstPurchase;
use MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedInCategory;
use MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedProduct;
use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\NewsletterSegmentEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Util\Helpers;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Query\Expr\Join;

/**
 * @extends Repository<NewsletterEntity>
 */
class NewslettersRepository extends Repository {
  private LoggerFactory $loggerFactory;

  public function __construct(
    EntityManager $entityManager
  ) {
    parent::__construct($entityManager);
    $this->loggerFactory = LoggerFactory::getInstance();
  }

  protected function getEntityClassName() {
    return NewsletterEntity::class;
  }

  /**
   * @param string[] $types
   * @return NewsletterEntity[]
   */
  public function findActiveByTypes($types) {
    return $this->entityManager
      ->createQueryBuilder()
      ->select('n')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.status = :status')
      ->setParameter(':status', NewsletterEntity::STATUS_ACTIVE)
      ->andWhere('n.deletedAt is null')
      ->andWhere('n.type IN (:types)')
      ->setParameter('types', $types)
      ->orderBy('n.subject')
      ->getQuery()
      ->getResult();
  }

  public function getCountForStatusAndTypes(string $status, array $types): int {
    return intval($this->entityManager
      ->createQueryBuilder()
      ->select('COUNT(n.id)')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.status = :status')
      ->andWhere('n.deletedAt is null')
      ->andWhere('n.type IN (:types)')
      ->setParameter('status', $status)
      ->setParameter('types', $types)
      ->getQuery()
      ->getSingleScalarResult());
  }

  public function getCountOfActiveAutomaticEmailsForEvent(string $event): int {
    return intval($this->entityManager->createQueryBuilder()
      ->select('COUNT(n.id)')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.status = :status')
      ->andWhere('n.deletedAt IS NULL')
      ->andWhere('n.type = :type')
      ->join('n.options', 'o', Join::WITH, 'o.value = :event')
      ->join('o.optionField', 'f', Join::WITH, 'f.name = :nameEvent AND f.newsletterType = :type')
      ->setParameter('status', NewsletterEntity::STATUS_ACTIVE)
      ->setParameter('nameEvent', NewsletterOptionFieldEntity::NAME_EVENT)
      ->setParameter('type', NewsletterEntity::TYPE_AUTOMATIC)
      ->setParameter('event', $event)
      ->getQuery()
      ->getSingleScalarResult());
  }

  public function getCountOfEmailsWithWPPost(): int {
    return intval($this->entityManager->createQueryBuilder()
      ->select('COUNT(n.id)')
      ->from(NewsletterEntity::class, 'n')
      ->andWhere('n.wpPost IS NOT NULL')
      ->getQuery()
      ->getSingleScalarResult());
  }

  /**
   * @return NewsletterEntity[]
   */
  public function findActiveByTypeAndGroup(string $type, ?string $group): array {
    $qb = $this->entityManager
      ->createQueryBuilder()
      ->select('n')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.status = :status')
      ->setParameter(':status', NewsletterEntity::STATUS_ACTIVE)
      ->andWhere('n.deletedAt IS NULL')
      ->andWhere('n.type = :type')
      ->setParameter('type', $type);

    if ($group) {
      $qb->join('n.options', 'o', Join::WITH, 'o.value = :group')
        ->join('o.optionField', 'f', Join::WITH, 'f.name = :nameGroup AND f.newsletterType = :type')
        ->setParameter('nameGroup', NewsletterOptionFieldEntity::NAME_GROUP)
        ->setParameter('group', $group);
    }

    return $qb->getQuery()->getResult();
  }

  /**
   * @param string[] $types
   * @return NewsletterEntity[]
   */
  public function findDraftByTypes($types) {
    return $this->entityManager
      ->createQueryBuilder()
      ->select('n')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.status = :status')
      ->setParameter(':status', NewsletterEntity::STATUS_DRAFT)
      ->andWhere('n.deletedAt is null')
      ->andWhere('n.type IN (:types)')
      ->setParameter('types', $types)
      ->orderBy('n.subject')
      ->getQuery()
      ->getResult();
  }

  public function getStandardNewsletterSentCount(DateTimeInterface $since): int {
    return (int)$this->doctrineRepository->createQueryBuilder('n')
      ->select('COUNT(n)')
      ->join('n.queues', 'q')
      ->join('q.task', 't')
      ->andWhere('n.type = :type')
      ->andWhere('n.status = :status')
      ->andWhere('t.status = :taskStatus')
      ->andWhere('t.processedAt >= :since')
      ->setParameter('type', NewsletterEntity::TYPE_STANDARD)
      ->setParameter('status', NewsletterEntity::STATUS_SENT)
      ->setParameter('taskStatus', ScheduledTaskEntity::STATUS_COMPLETED)
      ->setParameter('since', $since)
      ->getQuery()
      ->getSingleScalarResult() ?: 0;
  }

  public function getAnalytics(): array {
    // for automatic emails join 'event' newsletter option to further group the counts
    $eventOptionId = (int)$this->entityManager->createQueryBuilder()
      ->select('nof.id')
      ->from(NewsletterOptionFieldEntity::class, 'nof')
      ->andWhere('nof.newsletterType = :eventOptionFieldType')
      ->andWhere('nof.name = :eventOptionFieldName')
      ->setParameter('eventOptionFieldType', NewsletterEntity::TYPE_AUTOMATIC)
      ->setParameter('eventOptionFieldName', 'event')
      ->getQuery()
      ->getSingleScalarResult();

    $results = $this->doctrineRepository->createQueryBuilder('n')
      ->select('n.type, eventOption.value AS event, COUNT(n) AS cnt')
      ->leftJoin('n.options', 'eventOption', Join::WITH, "eventOption.optionField = :eventOptionId")
      ->andWhere('n.deletedAt IS NULL')
      ->andWhere('n.status IN (:statuses)')
      ->setParameter('eventOptionId', $eventOptionId)
      ->setParameter('statuses', [NewsletterEntity::STATUS_ACTIVE, NewsletterEntity::STATUS_SENT])
      ->groupBy('n.type, eventOption.value')
      ->getQuery()
      ->getResult();

    $analyticsMap = [];
    foreach ($results as $result) {
      $type = $result['type'];
      if ($type === NewsletterEntity::TYPE_AUTOMATIC) {
        $analyticsMap[$type][$result['event'] ?? ''] = (int)$result['cnt'];
      } else {
        $analyticsMap[$type] = (int)$result['cnt'];
      }
    }

    $data = [
      'welcome_newsletters_count' => $analyticsMap[NewsletterEntity::TYPE_WELCOME] ?? 0,
      'notifications_count' => $analyticsMap[NewsletterEntity::TYPE_NOTIFICATION] ?? 0,
      'automatic_emails_count' => array_sum($analyticsMap[NewsletterEntity::TYPE_AUTOMATIC] ?? []),
      'automation_emails_count' => $analyticsMap[NewsletterEntity::TYPE_AUTOMATION] ?? 0,
      're-engagement_emails_count' => $analyticsMap[NewsletterEntity::TYPE_RE_ENGAGEMENT] ?? 0,
      'sent_newsletters_count' => $analyticsMap[NewsletterEntity::TYPE_STANDARD] ?? 0,
      'sent_newsletters_7_days' => $this->getStandardNewsletterSentCount(Carbon::now()->subDays(7)),
      'sent_newsletters_3_months' => $this->getStandardNewsletterSentCount(Carbon::now()->subMonths(3)),
      'sent_newsletters_30_days' => $this->getStandardNewsletterSentCount(Carbon::now()->subDays(30)),
      'first_purchase_emails_count' => $analyticsMap[NewsletterEntity::TYPE_AUTOMATIC][FirstPurchase::SLUG] ?? 0,
      'product_purchased_emails_count' => $analyticsMap[NewsletterEntity::TYPE_AUTOMATIC][PurchasedProduct::SLUG] ?? 0,
      'product_purchased_in_category_emails_count' => $analyticsMap[NewsletterEntity::TYPE_AUTOMATIC][PurchasedInCategory::SLUG] ?? 0,
      'abandoned_cart_emails_count' => $analyticsMap[NewsletterEntity::TYPE_AUTOMATIC][AbandonedCart::SLUG] ?? 0,
    ];
    // Count all campaigns
    $analyticsMap[NewsletterEntity::TYPE_AUTOMATIC] = array_sum($analyticsMap[NewsletterEntity::TYPE_AUTOMATIC] ?? []);
    // Post notification history is not a campaign, we count only the parent notification
    unset($analyticsMap[NewsletterEntity::TYPE_NOTIFICATION_HISTORY]);
    $data['campaigns_count'] = array_sum($analyticsMap);
    return $data;
  }

  /**
   * @param array $params
   * @return NewsletterEntity[]
   */
  public function getArchives(array $params = []) {
    $types = [
      NewsletterEntity::TYPE_STANDARD,
      NewsletterEntity::TYPE_NOTIFICATION_HISTORY,
    ];

    $queryBuilder = $this->entityManager
      ->createQueryBuilder()
      ->select('n')
      ->distinct()
      ->from(NewsletterEntity::class, 'n')
      ->innerJoin(SendingQueueEntity::class, 'sq', Join::WITH, 'sq.newsletter = n.id')
      ->innerJoin(ScheduledTaskEntity::class, 'st', Join::WITH, 'st.id = sq.task')
      ->where('n.type IN (:types)')
      ->andWhere('st.status = :statusCompleted')
      ->andWhere('n.deletedAt IS NULL')
      ->orderBy('st.processedAt', 'DESC')
      ->addOrderBy('st.id', 'ASC')
      ->setParameter('types', $types)
      ->setParameter('statusCompleted', SendingQueueEntity::STATUS_COMPLETED);

    $segmentIds = $params['segmentIds'] ?? [];
    if (!empty($segmentIds)) {
      $queryBuilder->innerJoin(NewsletterSegmentEntity::class, 'ns', Join::WITH, 'ns.newsletter = n.id')
        ->andWhere('ns.segment IN (:segmentIds)')
        ->setParameter('segmentIds', $segmentIds);
    }

    $startDate = $params['startDate'] ?? null;
    if ($startDate instanceof DateTimeInterface) {
      $queryBuilder
        ->andWhere('st.processedAt >= :startDate')
        ->setParameter('startDate', $startDate);
    }

    $endDate = $params['endDate'] ?? null;
    if ($endDate instanceof DateTimeInterface) {
      $queryBuilder
        ->andWhere('st.processedAt <= :endDate')
        ->setParameter('endDate', $endDate);
    }

    $subjectContains = $params['subjectContains'] ?? null;
    if (is_string($subjectContains)) {
      $queryBuilder
        ->andWhere($queryBuilder->expr()->like('n.subject', ':subjectContains'))
        ->setParameter('subjectContains', '%' . Helpers::escapeSearch($subjectContains) . '%');
    }

    $limit = $params['limit'] ?? null;
    if (is_int($limit) && $limit > 0) {
      $queryBuilder->setMaxResults($limit);
    }

    return $queryBuilder->getQuery()->getResult();
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkTrash(array $ids): int {
    if (empty($ids)) {
      return 0;
    }
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS, $attachProcessors = true)->info(
      'trashing newsletters',
      ['id' => $ids]
    );
    // Fetch children id for trashing
    $childrenIds = $this->fetchChildrenIds($ids);
    $ids = array_merge($ids, $childrenIds);

    $this->entityManager->createQueryBuilder()
      ->update(NewsletterEntity::class, 'n')
      ->set('n.deletedAt', 'CURRENT_TIMESTAMP()')
      ->where('n.id IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()->execute();

    // Trash scheduled tasks
    $scheduledTasksTable = $this->entityManager->getClassMetadata(ScheduledTaskEntity::class)->getTableName();
    $sendingQueueTable = $this->entityManager->getClassMetadata(SendingQueueEntity::class)->getTableName();
    $this->entityManager->getConnection()->executeStatement("
       UPDATE $scheduledTasksTable t
       JOIN $sendingQueueTable q ON t.`id` = q.`task_id`
       SET t.`deleted_at` = NOW()
       WHERE q.`newsletter_id` IN (:ids)
    ", ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

    // Trash sending queues
    $this->entityManager->getConnection()->executeStatement("
       UPDATE $sendingQueueTable q
       SET q.`deleted_at` = NOW()
       WHERE q.`newsletter_id` IN (:ids)
    ", ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

    // Trash CPT.
    $wpPostIds = $this->getWpPostIds($ids);

    foreach ($wpPostIds as $wpPostId) {
      wp_trash_post($wpPostId);
    }

    return count($ids);
  }

  public function bulkRestore(array $ids) {
    if (empty($ids)) {
      return 0;
    }
    // Fetch children ids to restore
    $childrenIds = $this->fetchChildrenIds($ids);
    $ids = array_merge($ids, $childrenIds);

    $this->entityManager->createQueryBuilder()->update(NewsletterEntity::class, 'n')
      ->set('n.deletedAt', ':deletedAt')
      ->where('n.id IN (:ids)')
      ->setParameter('deletedAt', null)
      ->setParameter('ids', $ids)
      ->getQuery()->execute();

    // Restore scheduled tasks and pause running ones
    $scheduledTasksTable = $this->entityManager->getClassMetadata(ScheduledTaskEntity::class)->getTableName();
    $sendingQueueTable = $this->entityManager->getClassMetadata(SendingQueueEntity::class)->getTableName();
    $this->entityManager->getConnection()->executeStatement("
       UPDATE $scheduledTasksTable t
       JOIN $sendingQueueTable q ON t.`id` = q.`task_id`
       SET t.`deleted_at` = null, t.`status` = IFNULL(t.status, :pausedStatus)
       WHERE q.`newsletter_id` IN (:ids)
    ", [
      'ids' => $ids,
      'pausedStatus' => ScheduledTaskEntity::STATUS_PAUSED,
    ], [
      'ids' => Connection::PARAM_INT_ARRAY,
    ]);

    // Restore sending queues
    $this->entityManager->getConnection()->executeStatement("
       UPDATE $sendingQueueTable q
       SET q.`deleted_at` = null
       WHERE q.`newsletter_id` IN (:ids)
    ", ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

    // Untrash CPT.
    $wpPostIds = $this->getWpPostIds($ids);

    foreach ($wpPostIds as $wpPostId) {
      wp_untrash_post($wpPostId);
    }
    return count($ids);
  }

  /** @param int[] $ids */
  public function deleteByIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(NewsletterEntity::class, 'n')
      ->where('n.id IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (NewsletterEntity $entity) use ($ids) {
      return in_array($entity->getId(), $ids, true);
    });
  }

  /**
   * @return NewsletterEntity[]
   */
  public function findSendingNotificationHistoryWithoutPausedOrInvalidTask(NewsletterEntity $newsletter): array {
    return $this->entityManager->createQueryBuilder()
      ->select('n')
      ->from(NewsletterEntity::class, 'n')
      ->join('n.queues', 'q')
      ->join('q.task', 't')
      ->where('n.parent = :parent')
      ->andWhere('n.type = :type')
      ->andWhere('n.status = :status')
      ->andWhere('n.deletedAt IS NULL')
      ->andWhere('t.status != :taskStatusPaused')
      ->andWhere('t.status != :taskStatusInvalid')
      ->setParameter('parent', $newsletter)
      ->setParameter('type', NewsletterEntity::TYPE_NOTIFICATION_HISTORY)
      ->setParameter('status', NewsletterEntity::STATUS_SENDING)
      ->setParameter('taskStatusPaused', ScheduledTaskEntity::STATUS_PAUSED)
      ->setParameter('taskStatusInvalid', ScheduledTaskEntity::STATUS_INVALID)
      ->getQuery()->getResult();
  }

  /**
   * Returns standard newsletters ordered by sentAt
   * @return NewsletterEntity[]
   */
  public function getStandardNewsletterList(): array {
    return $this->entityManager->createQueryBuilder()
      ->select('PARTIAL n.{id,subject,sentAt}, PARTIAL wpPost.{id, postTitle}')
      ->addSelect('CASE WHEN n.sentAt IS NULL THEN 1 ELSE 0 END as HIDDEN sent_at_is_null')
      ->from(NewsletterEntity::class, 'n')
      ->leftJoin('n.wpPost', 'wpPost')
      ->where('n.type = :typeStandard')
      ->andWhere('n.deletedAt IS NULL')
      ->orderBy('sent_at_is_null', 'DESC')
      ->addOrderBy('n.sentAt', 'DESC')
      ->setParameter('typeStandard', NewsletterEntity::TYPE_STANDARD)
      ->getQuery()
      ->getResult();
  }

  /**
   * Returns standard newsletters ordered by sentAt
   * filter by status STATUS_SCHEDULED, STATUS_SENDING, STATUS_SENT
   * @return NewsletterEntity[]
   */
  public function getStandardNewsletterListWithMultipleStatuses($limit): array {
    $statuses = [
      NewsletterEntity::STATUS_SCHEDULED,
      NewsletterEntity::STATUS_SENDING,
      NewsletterEntity::STATUS_SENT,
    ];

    $query = $this->entityManager->createQueryBuilder()
      ->select('PARTIAL n.{id,subject,sentAt}')
      ->addSelect('CASE WHEN n.sentAt IS NULL THEN 1 ELSE 0 END as HIDDEN sent_at_is_null')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.type = :typeStandard')
      ->andWhere('n.status IN (:statuses)')
      ->andWhere('n.deletedAt IS NULL')
      ->orderBy('sent_at_is_null', 'DESC')
      ->addOrderBy('n.sentAt', 'DESC')
      ->setParameter('typeStandard', NewsletterEntity::TYPE_STANDARD)
      ->setParameter('statuses', $statuses);

    if (is_int($limit)) {
      $query->setMaxResults($limit);
    }

    $result = $query->getQuery()->getResult();

    return is_array($result) ? $result : [];
  }

  /**
   * Returns sent post-notification history newsletters ordered by sentAt
   * @return NewsletterEntity[]
   */
  public function getNotificationHistoryItems($limit): array {
    $query = $this->entityManager->createQueryBuilder()
      ->select('PARTIAL n.{id,subject,sentAt}')
      ->addSelect('CASE WHEN n.sentAt IS NULL THEN 1 ELSE 0 END as HIDDEN sent_at_is_null')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.type = :typeNotificationHistory')
      ->andWhere('n.status = :status')
      ->andWhere('n.deletedAt IS NULL')
      ->orderBy('sent_at_is_null', 'DESC')
      ->addOrderBy('n.sentAt', 'DESC')
      ->setParameter('typeNotificationHistory', NewsletterEntity::TYPE_NOTIFICATION_HISTORY)
      ->setParameter('status', NewsletterEntity::STATUS_SENT);

    if (is_int($limit)) {
      $query->setMaxResults($limit);
    }

    $result = $query->getQuery()->getResult();

    return is_array($result) ? $result : [];
  }

  public function prefetchOptions(array $newsletters) {
    $this->entityManager->createQueryBuilder()
      ->select('PARTIAL n.{id}, o, opf')
      ->from(NewsletterEntity::class, 'n')
      ->join('n.options', 'o')
      ->join('o.optionField', 'opf')
      ->where('n.id IN (:newsletters)')
      ->setParameter('newsletters', $newsletters)
      ->getQuery()
      ->getResult();
  }

  public function prefetchSegments(array $newsletters) {
    $this->entityManager->createQueryBuilder()
      ->select('PARTIAL n.{id}, ns, s')
      ->from(NewsletterEntity::class, 'n')
      ->join('n.newsletterSegments', 'ns')
      ->join('ns.segment', 's')
      ->where('n.id IN (:newsletters)')
      ->setParameter('newsletters', $newsletters)
      ->getQuery()
      ->getResult();
  }

  /**
   * Returns a list of emails that are either scheduled standard emails
   * or active automatic emails of the provided types.
   *
   * @param array $automaticEmailTypes
   *
   * @return array
   */
  public function getScheduledStandardEmailsAndActiveAutomaticEmails(array $automaticEmailTypes): array {
    $queryBuilder = $this->entityManager->createQueryBuilder();

    $newsletters = $queryBuilder
      ->select('n')
      ->from(NewsletterEntity::class, 'n')
      ->orWhere(
        $queryBuilder->expr()->andX(
          $queryBuilder->expr()->eq('n.type', ':typeStandard'),
          $queryBuilder->expr()->eq('n.status', ':statusScheduled')
        )
      )
      ->orWhere(
        $queryBuilder->expr()->andX(
          $queryBuilder->expr()->in('n.type', ':automaticEmailTypes'),
          $queryBuilder->expr()->eq('n.status', ':statusActive')
        )
      )
      ->setParameter('typeStandard', NewsletterEntity::TYPE_STANDARD)
      ->setParameter('statusScheduled', NewsletterEntity::STATUS_SCHEDULED)
      ->setParameter('automaticEmailTypes', $automaticEmailTypes)
      ->setParameter('statusActive', NewsletterEntity::STATUS_ACTIVE)
      ->getQuery()
      ->getResult();

    return $newsletters;
  }

  public function getCorruptNewsletters(): array {
    return $this->findBy(['status' => NewsletterEntity::STATUS_CORRUPT, 'deletedAt' => null]);
  }

  public function setAsCorrupt(NewsletterEntity $entity): void {
    $entity->setStatus(NewsletterEntity::STATUS_CORRUPT);
    $this->persist($entity);
    $this->flush();
  }

  /**
   * @param int[] $parentIds
   * @return int[]
   */
  public function fetchChildrenIds(array $parentIds): array {
    /** @var string[] $ids */
    $ids = $this->entityManager->createQueryBuilder()
      ->select('n.id')
      ->from(NewsletterEntity::class, 'n')
      ->where('n.parent IN (:ids)')
      ->setParameter('ids', $parentIds)
      ->getQuery()
      ->getSingleColumnResult();
    return array_map('intval', $ids);
  }

  public function getWpPostIds(array $ids): array {
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

      return $wpPostIds;
  }
}
