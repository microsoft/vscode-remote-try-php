<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Migrator\AppMigration;

/**
 * Due to a bug https://mailpoet.atlassian.net/browse/MAILPOET-5886
 * The status of newsletters was not updated to sent when the task was completed
 * In this migration we find newsletters with status sending and their tasks are completed and update their status to sent
 */
class Migration_20240202_130053_App extends AppMigration {
  public function run(): void {
    $affectedNewsletterIds = $this->entityManager->createQueryBuilder()
      ->select('n.id')
      ->from(NewsletterEntity::class, 'n')
      ->join('n.queues', 'q')
      ->join('q.task', 't')
      ->where('n.status = :status_sending')
      ->andWhere('t.status = :status_completed')
      ->setParameter('status_sending', NewsletterEntity::STATUS_SENDING)
      ->setParameter('status_completed', ScheduledTaskEntity::STATUS_COMPLETED)
      ->getQuery()
      ->getArrayResult();

    $affectedNewsletterIds = array_column($affectedNewsletterIds, 'id');

    $this->entityManager->createQueryBuilder()
      ->update(NewsletterEntity::class, 'n')
      ->set('n.status', ':status_sent')
      ->where('n.id IN (:ids)')
      ->setParameter('status_sent', NewsletterEntity::STATUS_SENT)
      ->setParameter('ids', $affectedNewsletterIds)
      ->getQuery()
      ->execute();
  }
}
