<?php declare(strict_types = 1);

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

/**
 * @extends Repository<StatisticsOpenEntity>
 */
class StatisticsOpensRepository extends Repository {
  /** @var SubscriberStatisticsRepository */
  private $subscriberStatisticsRepository;

  public function __construct(
    EntityManager $entityManager,
    SubscriberStatisticsRepository $subscriberStatisticsRepository
  ) {
    parent::__construct($entityManager);
    $this->entityManager = $entityManager;
    $this->subscriberStatisticsRepository = $subscriberStatisticsRepository;
  }

  protected function getEntityClassName(): string {
    return StatisticsOpenEntity::class;
  }

  public function recalculateSubscriberScore(SubscriberEntity $subscriber): void {
    $subscriber->setEngagementScoreUpdatedAt(new \DateTimeImmutable());
    $yearAgo = Carbon::now()->subYear();
    $newslettersSentCount = $this->subscriberStatisticsRepository->getTotalSentCount($subscriber, $yearAgo);
    if ($newslettersSentCount < 3) {
      $subscriber->setEngagementScore(null);
      $this->entityManager->flush();
      return;
    }
    $opensCount = $this->subscriberStatisticsRepository->getStatisticsOpenCount($subscriber, $yearAgo);
    $score = ($opensCount / $newslettersSentCount) * 100;
    $subscriber->setEngagementScore($score);
    $this->entityManager->flush();
  }

  public function resetSubscribersScoreCalculation() {
    $this->entityManager->createQueryBuilder()->update(SubscriberEntity::class, 's')
      ->set('s.engagementScoreUpdatedAt', ':updatedAt')
      ->setParameter('updatedAt', null)
      ->getQuery()->execute();
  }

  public function recalculateSegmentScore(SegmentEntity $segment): void {
    $segment->setAverageEngagementScoreUpdatedAt(new \DateTimeImmutable());
    $avgScore = $this
      ->entityManager
      ->createQueryBuilder()
      ->select('avg(subscriber.engagementScore)')
      ->from(SubscriberEntity::class, 'subscriber')
      ->join('subscriber.subscriberSegments', 'subscriberSegments')
      ->where('subscriberSegments.segment = :segment')
      ->andWhere('subscriber.status = :subscribed')
      ->andWhere('subscriber.deletedAt IS NULL')
      ->andWhere('subscriberSegments.status = :subscribed')
      ->setParameter('segment', $segment)
      ->setParameter('subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->getQuery()
      ->getSingleScalarResult();
    $segment->setAverageEngagementScore($avgScore === null ? $avgScore : (float)$avgScore);
    $this->entityManager->flush();
  }

  public function resetSegmentsScoreCalculation(): void {
    $this->entityManager->createQueryBuilder()->update(SegmentEntity::class, 's')
      ->set('s.averageEngagementScoreUpdatedAt', ':updatedAt')
      ->setParameter('updatedAt', null)
      ->getQuery()->execute();
  }

  public function getAllForSubscriber(SubscriberEntity $subscriber): QueryBuilder {
    return $this->entityManager->createQueryBuilder()
      ->select('opens.id id, queue.newsletterRenderedSubject, opens.createdAt, userAgent.userAgent')
      ->from(StatisticsOpenEntity::class, 'opens')
      ->join('opens.queue', 'queue')
      ->leftJoin('opens.userAgent', 'userAgent')
      ->where('opens.subscriber = :subscriber')
      ->orderBy('queue.newsletterRenderedSubject')
      ->setParameter('subscriber', $subscriber->getId());
  }

  /** @param int[] $ids */
  public function deleteByNewsletterIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(StatisticsOpenEntity::class, 's')
      ->where('s.newsletter IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (StatisticsOpenEntity $entity) use ($ids) {
      $newsletter = $entity->getNewsletter();
      return $newsletter && in_array($newsletter->getId(), $ids, true);
    });
  }
}
