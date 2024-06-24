<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use DateTimeInterface;
use MailPoet\Config\SubscriberChangesNotifier;
use MailPoet\Doctrine\Repository;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Entities\SubscriberTagEntity;
use MailPoet\Entities\TagEntity;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Util\License\Features\Subscribers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Query\Expr\Join;

/**
 * @extends Repository<SubscriberEntity>
 */
class SubscribersRepository extends Repository {
  /** @var WPFunctions */
  private $wp;

  protected $ignoreColumnsForUpdate = [
    'wp_user_id',
    'is_woocommerce_user',
    'email',
    'created_at',
    'last_subscribed_at',
  ];

  /** @var SubscriberChangesNotifier */
  private $changesNotifier;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    EntityManager $entityManager,
    SubscriberChangesNotifier $changesNotifier,
    WPFunctions $wp,
    SegmentsRepository $segmentsRepository
  ) {
    $this->wp = $wp;
    parent::__construct($entityManager);
    $this->changesNotifier = $changesNotifier;
    $this->segmentsRepository = $segmentsRepository;
  }

  protected function getEntityClassName() {
    return SubscriberEntity::class;
  }

  public function getTotalSubscribers(): int {
    return $this->getCountOfSubscribersForStates([
      SubscriberEntity::STATUS_SUBSCRIBED,
      SubscriberEntity::STATUS_UNCONFIRMED,
      SubscriberEntity::STATUS_INACTIVE,
    ]);
  }

  public function getCountOfSubscribersForStates(array $states): int {
    $query = $this->entityManager
      ->createQueryBuilder()
      ->select('count(n.id)')
      ->from(SubscriberEntity::class, 'n')
      ->where('n.deletedAt IS NULL AND n.status IN (:statuses)')
      ->setParameter('statuses', $states)
      ->getQuery();
    return intval($query->getSingleScalarResult());
  }

  public function invalidateTotalSubscribersCache(): void {
    $this->wp->deleteTransient(Subscribers::SUBSCRIBERS_COUNT_CACHE_KEY);
  }

  public function findBySegment(int $segmentId): array {
    return $this->entityManager
    ->createQueryBuilder()
    ->select('s')
    ->from(SubscriberEntity::class, 's')
    ->join('s.subscriberSegments', 'ss', Join::WITH, 'ss.segment = :segment')
    ->setParameter('segment', $segmentId)
    ->getQuery()->getResult();
  }

  public function findExclusiveSubscribersBySegment(int $segmentId): array {
    return $this->entityManager->createQueryBuilder()
      ->select('s')
      ->from(SubscriberEntity::class, 's')
      ->join('s.subscriberSegments', 'ss', Join::WITH, 'ss.segment = :segment')
      ->leftJoin('s.subscriberSegments', 'ss2', Join::WITH, 'ss2.segment <> :segment AND ss2.status = :subscribed')
      ->leftJoin('ss2.segment', 'seg', Join::WITH, 'seg.deletedAt IS NULL')
      ->groupBy('s.id')
      ->andHaving('COUNT(seg.id) = 0')
      ->setParameter('segment', $segmentId)
      ->setParameter('subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->getQuery()->getResult();
  }

  public function getWooCommerceSegmentSubscriber(string $email): ?SubscriberEntity {
    $subscriber = $this->doctrineRepository->createQueryBuilder('s')
      ->join('s.subscriberSegments', 'ss')
      ->join('ss.segment', 'sg', Join::WITH, 'sg.type = :typeWcUsers')
      ->where('s.isWoocommerceUser = 1')
      ->andWhere('s.status IN (:subscribed, :unconfirmed)')
      ->andWhere('ss.status = :subscribed')
      ->andWhere('s.email = :email')
      ->setParameter('typeWcUsers', SegmentEntity::TYPE_WC_USERS)
      ->setParameter('subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->setParameter('unconfirmed', SubscriberEntity::STATUS_UNCONFIRMED)
      ->setParameter('email', $email)
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
    return $subscriber instanceof SubscriberEntity ? $subscriber : null;
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkTrash(array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $this->entityManager->createQueryBuilder()
      ->update(SubscriberEntity::class, 's')
      ->set('s.deletedAt', 'CURRENT_TIMESTAMP()')
      ->where('s.id IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()->execute();

    $this->changesNotifier->subscribersUpdated($ids);
    $this->invalidateTotalSubscribersCache();
    return count($ids);
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkRestore(array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $this->entityManager->createQueryBuilder()
      ->update(SubscriberEntity::class, 's')
      ->set('s.deletedAt', ':deletedAt')
      ->where('s.id IN (:ids)')
      ->setParameter('deletedAt', null)
      ->setParameter('ids', $ids)
      ->getQuery()->execute();

    $this->changesNotifier->subscribersUpdated($ids);
    $this->invalidateTotalSubscribersCache();
    return count($ids);
  }

   /**
   * @return int - number of processed ids
   */
  public function bulkDelete(array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $count = 0;
    $this->entityManager->transactional(function (EntityManager $entityManager) use ($ids, &$count) {
      // Delete subscriber segments
      $this->removeSubscribersFromAllSegments($ids);

      // Delete subscriber custom fields
      $subscriberCustomFieldTable = $entityManager->getClassMetadata(SubscriberCustomFieldEntity::class)->getTableName();
      $subscriberTable = $entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
      $entityManager->getConnection()->executeStatement("
         DELETE scs FROM $subscriberCustomFieldTable scs
         JOIN $subscriberTable s ON s.`id` = scs.`subscriber_id`
         WHERE scs.`subscriber_id` IN (:ids)
         AND s.`is_woocommerce_user` = false
         AND s.`wp_user_id` IS NULL
      ", ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

      // Delete subscriber tags
      $subscriberTagTable = $entityManager->getClassMetadata(SubscriberTagEntity::class)->getTableName();
      $entityManager->getConnection()->executeStatement("
         DELETE st FROM $subscriberTagTable st
         JOIN $subscriberTable s ON s.`id` = st.`subscriber_id`
         WHERE st.`subscriber_id` IN (:ids)
         AND s.`is_woocommerce_user` = false
         AND s.`wp_user_id` IS NULL
      ", ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

      $queryBuilder = $entityManager->createQueryBuilder();
      $count = $queryBuilder->delete(SubscriberEntity::class, 's')
        ->where('s.id IN (:ids)')
        ->andWhere('s.wpUserId IS NULL')
        ->andWhere('s.isWoocommerceUser = false')
        ->setParameter('ids', $ids)
        ->getQuery()->execute();
    });

    $this->changesNotifier->subscribersDeleted($ids);
    $this->invalidateTotalSubscribersCache();
    return $count;
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkRemoveFromSegment(SegmentEntity $segment, array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $subscriberSegmentsTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $count = (int)$this->entityManager->getConnection()->executeStatement("
       DELETE ss FROM $subscriberSegmentsTable ss
       WHERE ss.`subscriber_id` IN (:ids)
       AND ss.`segment_id` = :segment_id
    ", ['ids' => $ids, 'segment_id' => $segment->getId()], ['ids' => Connection::PARAM_INT_ARRAY]);

    $this->changesNotifier->subscribersUpdated($ids);
    return $count;
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkRemoveFromAllSegments(array $ids): int {
    $count = $this->removeSubscribersFromAllSegments($ids);
    $this->changesNotifier->subscribersUpdated($ids);
    return $count;
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkAddToSegment(SegmentEntity $segment, array $ids): int {
    $count = $this->addSubscribersToSegment($segment, $ids);
    $this->changesNotifier->subscribersUpdated($ids);
    return $count;
  }

  public function woocommerceUserExists(): bool {
    $subscribers = $this->entityManager
      ->createQueryBuilder()
      ->select('s')
      ->from(SubscriberEntity::class, 's')
      ->join('s.subscriberSegments', 'ss')
      ->join('ss.segment', 'segment')
      ->where('segment.type = :segmentType')
      ->setParameter('segmentType', SegmentEntity::TYPE_WC_USERS)
      ->andWhere('s.isWoocommerceUser = true')
      ->getQuery()
      ->setMaxResults(1)
      ->execute();

    return count($subscribers) > 0;
  }

   /**
   * @return int - number of processed ids
   */
  public function bulkMoveToSegment(SegmentEntity $segment, array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $this->removeSubscribersFromAllSegments($ids);
    $count = $this->addSubscribersToSegment($segment, $ids);

    $this->changesNotifier->subscribersUpdated($ids);
    return $count;
  }

  public function bulkUnsubscribe(array $ids): int {
    $this->entityManager->createQueryBuilder()
      ->update(SubscriberEntity::class, 's')
      ->set('s.status', ':status')
      ->where('s.id IN (:ids)')
      ->setParameter('status', SubscriberEntity::STATUS_UNSUBSCRIBED)
      ->setParameter('ids', $ids)
      ->getQuery()->execute();

    $this->changesNotifier->subscribersUpdated($ids);
    $this->invalidateTotalSubscribersCache();
    return count($ids);
  }

  public function bulkUpdateLastSendingAt(array $ids, DateTimeInterface $dateTime): int {
    if (empty($ids)) {
      return 0;
    }
    $this->entityManager->createQueryBuilder()
      ->update(SubscriberEntity::class, 's')
      ->set('s.lastSendingAt', ':lastSendingAt')
      ->where('s.id IN (:ids)')
      ->setParameter('lastSendingAt', $dateTime)
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();
    return count($ids);
  }

  public function bulkUpdateEngagementScoreUpdatedAt(array $ids, ?DateTimeInterface $dateTime): void {
    if (empty($ids)) {
      return;
    }
    $this->entityManager->createQueryBuilder()
      ->update(SubscriberEntity::class, 's')
      ->set('s.engagementScoreUpdatedAt', ':dateTime')
      ->where('s.id IN (:ids)')
      ->setParameter('dateTime', $dateTime)
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();
  }

  public function findWpUserIdAndEmailByEmails(array $emails): array {
    return $this->entityManager->createQueryBuilder()
      ->select('s.wpUserId AS wp_user_id, LOWER(s.email) AS email')
      ->from(SubscriberEntity::class, 's')
      ->where('s.email IN (:emails)')
      ->setParameter('emails', $emails)
      ->getQuery()->getResult();
  }

  public function findIdAndEmailByEmails(array $emails): array {
    return $this->entityManager->createQueryBuilder()
      ->select('s.id, s.email')
      ->from(SubscriberEntity::class, 's')
      ->where('s.email IN (:emails)')
      ->setParameter('emails', $emails)
      ->getQuery()->getResult();
  }

  /**
   * @return int[]
   */
  public function findIdsOfDeletedByEmails(array $emails): array {
    return $this->entityManager->createQueryBuilder()
    ->select('s.id')
    ->from(SubscriberEntity::class, 's')
    ->where('s.email IN (:emails)')
    ->andWhere('s.deletedAt IS NOT NULL')
    ->setParameter('emails', $emails)
    ->getQuery()->getResult();
  }

  public function getCurrentWPUser(): ?SubscriberEntity {
    $wpUser = WPFunctions::get()->wpGetCurrentUser();
    if (empty($wpUser->ID)) {
      return null; // Don't look up a subscriber for guests
    }
    return $this->findOneBy(['wpUserId' => $wpUser->ID]);
  }

  public function findByUpdatedScoreNotInLastMonth(int $limit): array {
    $dateTime = (new Carbon())->subMonths(1);
    return $this->entityManager->createQueryBuilder()
      ->select('s')
      ->from(SubscriberEntity::class, 's')
      ->where('s.engagementScoreUpdatedAt IS NULL')
      ->orWhere('s.engagementScoreUpdatedAt < :dateTime')
      ->setParameter('dateTime', $dateTime)
      ->getQuery()
      ->setMaxResults($limit)
      ->getResult();
  }

  public function maybeUpdateLastEngagement(SubscriberEntity $subscriberEntity): void {
    $now = $this->getCurrentDateTime();
    // Do not update engagement if was recently updated to avoid unnecessary updates in DB
    if ($subscriberEntity->getLastEngagementAt() && $subscriberEntity->getLastEngagementAt() > $now->subMinute()) {
      return;
    }
    // Update last engagement
    $subscriberEntity->setLastEngagementAt($now);
    $this->flush();
  }

  public function maybeUpdateLastOpenAt(SubscriberEntity $subscriberEntity): void {
    $now = $this->getCurrentDateTime();
    // Avoid unnecessary DB calls
    if ($subscriberEntity->getLastOpenAt() && $subscriberEntity->getLastOpenAt() > $now->subMinute()) {
      return;
    }
    $subscriberEntity->setLastOpenAt($now);
    $subscriberEntity->setLastEngagementAt($now);
    $this->flush();
  }

  public function maybeUpdateLastClickAt(SubscriberEntity $subscriberEntity): void {
    $now = $this->getCurrentDateTime();
    // Avoid unnecessary DB calls
    if ($subscriberEntity->getLastClickAt() && $subscriberEntity->getLastClickAt() > $now->subMinute()) {
      return;
    }
    $subscriberEntity->setLastClickAt($now);
    $subscriberEntity->setLastEngagementAt($now);
    $this->flush();
  }

  public function maybeUpdateLastPurchaseAt(SubscriberEntity $subscriberEntity): void {
    $now = $this->getCurrentDateTime();
    // Avoid unnecessary DB calls
    if ($subscriberEntity->getLastPurchaseAt() && $subscriberEntity->getLastPurchaseAt() > $now->subMinute()) {
      return;
    }
    $subscriberEntity->setLastPurchaseAt($now);
    $subscriberEntity->setLastEngagementAt($now);
    $this->flush();
  }

  public function maybeUpdateLastPageViewAt(SubscriberEntity $subscriberEntity): void {
    $now = $this->getCurrentDateTime();
    // Avoid unnecessary DB calls
    if ($subscriberEntity->getLastPageViewAt() && $subscriberEntity->getLastPageViewAt() > $now->subMinute()) {
      return;
    }
    $subscriberEntity->setLastPageViewAt($now);
    $subscriberEntity->setLastEngagementAt($now);
    $this->flush();
  }

  /**
   * @param array $ids
   * @return string[]
   */
  public function getUndeletedSubscribersEmailsByIds(array $ids): array {
    return $this->entityManager->createQueryBuilder()
      ->select('s.email')
      ->from(SubscriberEntity::class, 's')
      ->where('s.deletedAt IS NULL')
      ->andWhere('s.id IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->getArrayResult();
  }

  public function getMaxSubscriberId(): int {
    $maxSubscriberId = $this->entityManager->createQueryBuilder()
      ->select('MAX(s.id)')
      ->from(SubscriberEntity::class, 's')
      ->getQuery()
      ->getSingleScalarResult();

    return intval($maxSubscriberId);
  }

  /**
   * Returns count of subscribers who subscribed after given date regardless of their current status.
   * @return int
   */
  public function getCountOfLastSubscribedAfter(\DateTimeInterface $subscribedAfter): int {
    $result = $this->entityManager->createQueryBuilder()
      ->select('COUNT(s.id)')
      ->from(SubscriberEntity::class, 's')
      ->where('s.lastSubscribedAt > :lastSubscribedAt')
      ->andWhere('s.deletedAt IS NULL')
      ->setParameter('lastSubscribedAt', $subscribedAfter)
      ->getQuery()
      ->getSingleScalarResult();
    return intval($result);
  }

  /**
   * Returns count of subscribers who unsubscribed after given date regardless of their current status.
   * @return int
   */
  public function getCountOfUnsubscribedAfter(\DateTimeInterface $unsubscribedAfter): int {
    $result = $this->entityManager->createQueryBuilder()
      ->select('COUNT(DISTINCT s.id)')
      ->from(StatisticsUnsubscribeEntity::class, 'su')
      ->join('su.subscriber', 's')
      ->andWhere('su.createdAt > :unsubscribedAfter')
      ->andWhere('s.deletedAt IS NULL')
      ->setParameter('unsubscribedAfter', $unsubscribedAfter)
      ->getQuery()
      ->getSingleScalarResult();
    return intval($result);
  }

  /**
   * Returns count of subscribers who subscribed to a list after given date regardless of their current global status.
   */
  public function getListLevelCountsOfSubscribedAfter(\DateTimeInterface $date): array {
    $data = $this->entityManager->createQueryBuilder()
      ->select('seg.id, seg.name, seg.type, seg.averageEngagementScore, COUNT(ss.id) as count')
      ->from(SubscriberSegmentEntity::class, 'ss')
      ->join('ss.subscriber', 's')
      ->join('ss.segment', 'seg')
      ->where('ss.updatedAt > :date')
      ->andWhere('ss.status = :segment_status')
      ->andWhere('s.lastSubscribedAt > :date') // subscriber subscribed at some point after the date
      ->andWhere('s.deletedAt IS NULL')
      ->andWhere('seg.deletedAt IS NULL') // no trashed lists and disabled WP Users list
      ->setParameter('date', $date)
      ->setParameter('segment_status', SubscriberEntity::STATUS_SUBSCRIBED)
      ->groupBy('ss.segment')
      ->getQuery()
      ->getArrayResult();
    return $data;
  }

  /**
   * Returns count of subscribers who unsubscribed from a list after given date regardless of their current global status.
   */
  public function getListLevelCountsOfUnsubscribedAfter(\DateTimeInterface $date): array {
    return $this->entityManager->createQueryBuilder()
      ->select('seg.id, seg.name, seg.type, seg.averageEngagementScore, COUNT(ss.id) as count')
      ->from(SubscriberSegmentEntity::class, 'ss')
      ->join('ss.subscriber', 's')
      ->join('ss.segment', 'seg')
      ->where('ss.updatedAt > :date')
      ->andWhere('ss.status = :segment_status')
      ->andWhere('s.deletedAt IS NULL')
      ->andWhere('seg.deletedAt IS NULL') // no trashed lists and disabled WP Users list
      ->setParameter('date', $date)
      ->setParameter('segment_status', SubscriberEntity::STATUS_UNSUBSCRIBED)
      ->groupBy('ss.segment')
      ->getQuery()
      ->getArrayResult();
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkAddTag(TagEntity $tag, array $ids): int {
    $count = $this->addTagToSubscribers($tag, $ids);
    $this->changesNotifier->subscribersUpdated($ids);
    return $count;
  }

  /**
   * @return int - number of processed ids
   */
  public function bulkRemoveTag(TagEntity $tag, array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $subscriberTagsTable = $this->entityManager->getClassMetadata(SubscriberTagEntity::class)->getTableName();
    $count = (int)$this->entityManager->getConnection()->executeStatement("
       DELETE st FROM $subscriberTagsTable st
       WHERE st.`subscriber_id` IN (:ids)
       AND st.`tag_id` = :tag_id
    ", ['ids' => $ids, 'tag_id' => $tag->getId()], ['ids' => Connection::PARAM_INT_ARRAY]);

    $this->changesNotifier->subscribersUpdated($ids);
    return $count;
  }

  public function removeOrphanedSubscribersFromWpSegment(): void {
    global $wpdb;

    $segmentId = $this->segmentsRepository->getWpUsersSegment()->getId();

    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $subscriberSegmentsTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();

    $this->entityManager->getConnection()->executeStatement(
      "DELETE s
       FROM {$subscribersTable} s
       INNER JOIN {$subscriberSegmentsTable} ss ON s.id = ss.subscriber_id
       LEFT JOIN {$wpdb->users} u ON s.wp_user_id = u.id
       WHERE ss.segment_id = :segmentId AND (u.id IS NULL OR s.email = '')",
      ['segmentId' => $segmentId],
      ['segmentId' => \PDO::PARAM_INT]
    );
  }

  public function removeByWpUserIds(array $wpUserIds) {
    $queryBuilder = $this->entityManager->createQueryBuilder();

    $queryBuilder
      ->delete(SubscriberEntity::class, 's')
      ->where('s.wpUserId IN (:wpUserIds)')
      ->setParameter('wpUserIds', $wpUserIds);

    return $queryBuilder->getQuery()->execute();
  }

  /**
   * @return int - number of processed ids
   */
  private function removeSubscribersFromAllSegments(array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $subscriberSegmentsTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $segmentsTable = $this->entityManager->getClassMetadata(SegmentEntity::class)->getTableName();
    $count = (int)$this->entityManager->getConnection()->executeStatement("
       DELETE ss FROM $subscriberSegmentsTable ss
       JOIN $segmentsTable s ON s.id = ss.segment_id AND s.`type` = :typeDefault
       WHERE ss.`subscriber_id` IN (:ids)
    ", [
      'ids' => $ids,
      'typeDefault' => SegmentEntity::TYPE_DEFAULT,
    ], ['ids' => Connection::PARAM_INT_ARRAY]);

    return $count;
  }

  /**
   * @return int - number of processed ids
   */
  private function addSubscribersToSegment(SegmentEntity $segment, array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    $subscribers = $this->entityManager
      ->createQueryBuilder()
      ->select('s')
      ->from(SubscriberEntity::class, 's')
      ->leftJoin('s.subscriberSegments', 'ss', Join::WITH, 'ss.segment = :segment')
      ->where('s.id IN (:ids)')
      ->andWhere('ss.segment IS NULL')
      ->setParameter('ids', $ids)
      ->setParameter('segment', $segment)
      ->getQuery()->execute();

    $this->entityManager->transactional(function (EntityManager $entityManager) use ($subscribers, $segment) {
      foreach ($subscribers as $subscriber) {
        $subscriberSegment = new SubscriberSegmentEntity($segment, $subscriber, SubscriberEntity::STATUS_SUBSCRIBED);
        $this->entityManager->persist($subscriberSegment);
      }
      $this->entityManager->flush();
    });

    return count($subscribers);
  }

  /**
   * @return int - number of processed ids
   */
  private function addTagToSubscribers(TagEntity $tag, array $ids): int {
    if (empty($ids)) {
      return 0;
    }

    /** @var SubscriberEntity[] $subscribers */
    $subscribers = $this->entityManager
      ->createQueryBuilder()
      ->select('s')
      ->from(SubscriberEntity::class, 's')
      ->leftJoin('s.subscriberTags', 'st', Join::WITH, 'st.tag = :tag')
      ->where('s.id IN (:ids)')
      ->andWhere('st.tag IS NULL')
      ->setParameter('ids', $ids)
      ->setParameter('tag', $tag)
      ->getQuery()->execute();

    $this->entityManager->wrapInTransaction(function (EntityManager $entityManager) use ($subscribers, $tag) {
      foreach ($subscribers as $subscriber) {
        $subscriberTag = new SubscriberTagEntity($tag, $subscriber);
        $entityManager->persist($subscriberTag);
      }
      $entityManager->flush();
    });

    return count($subscribers);
  }

  private function getCurrentDateTime(): CarbonImmutable {
    return CarbonImmutable::createFromTimestamp((int)$this->wp->currentTime('timestamp'));
  }
}
