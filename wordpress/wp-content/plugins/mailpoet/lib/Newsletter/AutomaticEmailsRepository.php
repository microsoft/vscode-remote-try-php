<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

/**
 * @extends Repository<NewsletterEntity>
 */
class AutomaticEmailsRepository extends Repository {
  protected function getEntityClassName() {
    return NewsletterEntity::class;
  }

  public function wasScheduledForSubscriber(int $newsletterId, int $subscriberId): bool {
    $query = $this->doctrineRepository->createQueryBuilder('n')
      ->select('COUNT(q)')
      ->from(SendingQueueEntity::class, 'q');
    $query = $this->getAllQueuesForSubscscriberQuery($query, $newsletterId, $subscriberId);
    $count = $query->getQuery()
      ->getSingleScalarResult() ?: 0;
    return ((int)$count) > 0;
  }

  private function getAllQueuesForSubscscriberQuery(QueryBuilder $query, int $newsletterId, int $subscriberId): QueryBuilder {
    return $query
      ->join('q.task', 't')
      ->join('t.subscribers', 's')
      ->andWhere('q.newsletter = :newsletterId')
      ->andWhere('s.subscriber = :subscriberId')
      ->setParameter('newsletterId', $newsletterId)
      ->setParameter('subscriberId', $subscriberId);
  }

  /**
   * Search products/categories in meta if all of the ordered products have already been sent to the subscriber.
   */
  public function alreadySentAllProducts(int $newsletterId, int $subscriberId, string $orderedKey, array $ordered): bool {
    $query = $this->doctrineRepository->createQueryBuilder('n')
      ->select('q')
      ->from(SendingQueueEntity::class, 'q');
    $queues = $this->getAllQueuesForSubscscriberQuery($query, $newsletterId, $subscriberId)
      ->getQuery()
      ->getResult();
    $sent = [];
    foreach ($queues as $queue) {
      $meta = $queue->getMeta();
      if (isset($meta[$orderedKey])) {
        $sent = array_merge($sent, $meta[$orderedKey]);
      }
    }
    $notSentProducts = array_diff($ordered, $sent);

    return empty($notSentProducts);
  }
}
