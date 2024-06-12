<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\StatsNotifications;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\StatsNotificationEntity;
use MailPoetVendor\Carbon\Carbon;

/**
 * @extends Repository<StatsNotificationEntity>
 */
class StatsNotificationsRepository extends Repository {
  protected function getEntityClassName() {
    return StatsNotificationEntity::class;
  }

  /**
   * @param int $newsletterId
   * @return StatsNotificationEntity|null
   */
  public function findOneByNewsletterId($newsletterId) {
    return $this->doctrineRepository
      ->createQueryBuilder('sn')
      ->andWhere('sn.newsletter = :newsletterId')
      ->setParameter('newsletterId', $newsletterId)
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }

  /**
   * @param int|null $limit
   * @return StatsNotificationEntity[]
   */
  public function findScheduled($limit = null) {
    $date = new Carbon();
    $query = $this->doctrineRepository
      ->createQueryBuilder('sn')
      ->join('sn.task', 'tasks')
      ->join('sn.newsletter', 'n')
      ->addSelect('tasks')
      ->addSelect('n')
      ->addOrderBy('tasks.priority')
      ->addOrderBy('tasks.updatedAt')
      ->where('tasks.deletedAt IS NULL')
      ->andWhere('tasks.status = :status')
      ->setParameter('status', ScheduledTaskEntity::STATUS_SCHEDULED)
      ->andWhere('tasks.scheduledAt < :date')
      ->setParameter('date', $date)
      ->andWhere('tasks.type = :workerType')
      ->setParameter('workerType', Worker::TASK_TYPE);
    if (is_int($limit)) {
      $query->setMaxResults($limit);
    }
    return $query->getQuery()->getResult();
  }

  public function deleteOrphanedScheduledTasks() {
    $scheduledTasksTable = $this->entityManager->getClassMetadata(ScheduledTaskEntity::class)->getTableName();
    $statsNotificationsTable = $this->entityManager->getClassMetadata(StatsNotificationEntity::class)->getTableName();
    $this->entityManager->getConnection()->executeStatement("
       DELETE st FROM $scheduledTasksTable st
       LEFT JOIN $statsNotificationsTable sn ON sn.task_id = st.id
       WHERE sn.id IS NULL AND st.type = :taskType;
    ", ['taskType' => Worker::TASK_TYPE]);
  }

  /** @param int[] $ids */
  public function deleteByNewsletterIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(StatsNotificationEntity::class, 'n')
      ->where('n.newsletter IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (StatsNotificationEntity $entity) use ($ids) {
      $newsletter = $entity->getNewsletter();
      return $newsletter && in_array($newsletter->getId(), $ids, true);
    });
  }
}
