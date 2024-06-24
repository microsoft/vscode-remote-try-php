<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Query\Expr\Join;

/**
 * @extends Repository<SubscriberSegmentEntity>
 */
class SubscriberSegmentRepository extends Repository {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    EntityManager $entityManager,
    WPFunctions $wp
  ) {
    parent::__construct($entityManager);
    $this->wp = $wp;
  }

  protected function getEntityClassName() {
    return SubscriberSegmentEntity::class;
  }

  public function getNonDefaultSubscribedSegments(int $subscriberId): array {
    $qb = $this->entityManager->createQueryBuilder();
    return $qb->select('ss')
      ->from(SubscriberSegmentEntity::class, 'ss')
      ->join('ss.segment', 'seg', Join::WITH, 'seg.type != :typeDefault')
      ->where('ss.subscriber = :subscriberId')
      ->andWhere('ss.status = :subscribed')
      ->setParameter('subscriberId', $subscriberId)
      ->setParameter('subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->setParameter('typeDefault', SegmentEntity::TYPE_DEFAULT)
      ->getQuery()
      ->getResult();
  }

  /**
   * @param SegmentEntity[] $segments
   */
  public function unsubscribeFromSegments(SubscriberEntity $subscriber, array $segments = []): void {
    $subscriber->setConfirmationsCount(0);

    if (!empty($segments)) {
      // unsubscribe from segments
      foreach ($segments as $segment) {
        // do not remove subscriptions to the WP Users segment
        if ($segment->getType() === SegmentEntity::TYPE_WP_USERS) {
          continue;
        }

        $this->createOrUpdate($subscriber, $segment, SubscriberEntity::STATUS_UNSUBSCRIBED);
      }
      $this->entityManager->flush();
    } else {
      $subscriberSegmentTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
      $segmentTable = $this->entityManager->getClassMetadata(SegmentEntity::class)->getTableName();
      $this->entityManager->getConnection()->executeStatement("
         UPDATE $subscriberSegmentTable ss
         JOIN $segmentTable s ON s.`id` = ss.`segment_id` AND ss.`subscriber_id` = :subscriberId
         SET ss.`status` = :status
         WHERE s.`type` != :typeWordPress
      ", [
        'subscriberId' => $subscriber->getId(),
        'status' => SubscriberEntity::STATUS_UNSUBSCRIBED,
        'typeWordPress' => SegmentEntity::TYPE_WP_USERS,
      ]);
      // Refresh SubscriberSegments status
      foreach ($subscriber->getSubscriberSegments() as $subscriberSegment) {
        $this->entityManager->refresh($subscriberSegment);
      }
    }
  }

  public function resetSubscriptions(SubscriberEntity $subscriber, array $segments): void {
    // Already existing subscriptions are stored in $existingSegments. Their IDs in $existingSegmentIds.
    $existingSegments = array_values(array_filter(array_map(
      function(SubscriberSegmentEntity $subscriberSegmentEntity): ?SegmentEntity {
        return $subscriberSegmentEntity->getSegment();
      },
      $this->findBy(['subscriber' => $subscriber, 'status' => SubscriberEntity::STATUS_SUBSCRIBED])
    )));
    $existingSegmentIds = array_map(
      function(SegmentEntity $segment): int {
        return $segment->getId() ?? 0;
      },
      $existingSegments
    );

    // $segmentIds are the IDs of the segments we want the user to be subscribed to.
    $segmentIds = array_map(
      function(SegmentEntity $segment): int {
        return $segment->getId() ?? 0;
      },
      $segments
    );

    // $unsubscribedSegments are the segment IDs to which we need to unsubscribe.
    $unsubscribedSegments = array_diff($existingSegmentIds, $segmentIds);

    // $newlySubscribedSegments are the segment IDs to which we need to newly subscribe.
    $newlySubscribedSegments = array_diff($segmentIds, $existingSegmentIds);
    if (!$newlySubscribedSegments && !$unsubscribedSegments) {
      return;
    }

    // The segments we need to unsubscribe.
    $unsubscribe = array_filter(
      $existingSegments,
      function(SegmentEntity $segment) use ($unsubscribedSegments): bool {
        return in_array($segment->getId(), $unsubscribedSegments);
      }
    );

    // The segments we need to newly subscribe.
    $subscribe = array_filter(
      $segments,
      function(SegmentEntity $segment) use ($newlySubscribedSegments): bool {
        return in_array($segment->getId(), $newlySubscribedSegments);
      }
    );
    if ($unsubscribe) {
      $this->unsubscribeFromSegments($subscriber, $unsubscribe);
    }
    if ($subscribe) {
      $this->subscribeToSegments($subscriber, $subscribe);
    }
  }

  /**
   * @param SegmentEntity[] $segments
   */
  public function subscribeToSegments(SubscriberEntity $subscriber, array $segments, bool $skipHooks = false): void {
    foreach ($segments as $segment) {
      $this->createOrUpdate($subscriber, $segment, SubscriberEntity::STATUS_SUBSCRIBED, $skipHooks);
    }
  }

  public function createOrUpdate(
    SubscriberEntity $subscriber,
    SegmentEntity $segment,
    string $status,
    bool $skipHooks = false
  ): SubscriberSegmentEntity {
    $subscriberSegment = $this->findOneBy(['segment' => $segment, 'subscriber' => $subscriber]);

    $oldStatus = null;
    if ($subscriberSegment instanceof SubscriberSegmentEntity) {
      $oldStatus = $subscriberSegment->getStatus();
      $subscriberSegment->setStatus($status);
    } else {
      $subscriberSegment = new SubscriberSegmentEntity($segment, $subscriber, $status);
      $subscriber->getSubscriberSegments()->add($subscriberSegment);
      $this->entityManager->persist($subscriberSegment);
    }
    $this->entityManager->flush();

    // fire subscribed hook for new subscriptions
    if (
      !$skipHooks
      && $subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED
      && $subscriberSegment->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED
      && $oldStatus !== SubscriberEntity::STATUS_SUBSCRIBED
    ) {
      $this->wp->doAction('mailpoet_segment_subscribed', $subscriberSegment);
    }

    return $subscriberSegment;
  }
}
