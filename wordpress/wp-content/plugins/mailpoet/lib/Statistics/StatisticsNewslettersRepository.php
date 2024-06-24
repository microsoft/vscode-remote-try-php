<?php declare(strict_types = 1);

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Carbon\Carbon;

/**
 * @extends Repository<StatisticsNewsletterEntity>
 */
class StatisticsNewslettersRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsNewsletterEntity::class;
  }

  public function createMultiple(array $data): void {
    $entities = [];

    foreach ($data as $value) {
      if (!empty($value['newsletter_id']) && !empty($value['queue_id']) && !empty($value['subscriber_id'])) {
        $newsletter = $this->entityManager->getReference(NewsletterEntity::class, $value['newsletter_id']);
        $queue = $this->entityManager->getReference(SendingQueueEntity::class, $value['queue_id']);
        $subscriber = $this->entityManager->getReference(SubscriberEntity::class, $value['subscriber_id']);

        if (!$newsletter || !$queue || !$subscriber) {
          continue;
        }

        $sentAt = Carbon::createFromTimestamp((int)current_time('timestamp'));
        $entity = new StatisticsNewsletterEntity($newsletter, $queue, $subscriber, $sentAt);

        $this->entityManager->persist($entity);
        $entities[] = $entity;
      }
    }

    if (count($entities)) {
      $this->entityManager->flush();
    }
  }

  /** @param int[] $ids */
  public function deleteByNewsletterIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(StatisticsNewsletterEntity::class, 's')
      ->where('s.newsletter IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (StatisticsNewsletterEntity $entity) use ($ids) {
      $newsletter = $entity->getNewsletter();
      return $newsletter && in_array($newsletter->getId(), $ids, true);
    });
  }
}
