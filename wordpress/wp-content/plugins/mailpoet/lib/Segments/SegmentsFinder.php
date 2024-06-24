<?php declare(strict_types = 1);

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\DynamicSegments\FilterHandler;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SegmentsFinder {
  /** @var EntityManager */
  private $entityManager;

  /** @var FilterHandler */
  private $filterHandler;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    EntityManager $entityManager,
    FilterHandler $filterHandler,
    SegmentsRepository $segmentsRepository
  ) {
    $this->entityManager = $entityManager;
    $this->filterHandler = $filterHandler;
    $this->segmentsRepository = $segmentsRepository;
  }

  /** @return SegmentEntity[] */
  public function findSegments(SubscriberEntity $subscriber): array {
    return array_merge(
      $this->findStaticSegments($subscriber),
      $this->findDynamicSegments($subscriber)
    );
  }

  /** @return SegmentEntity[] */
  public function findStaticSegments(SubscriberEntity $subscriber): array {
    return $subscriber->getSegments()->toArray();
  }

  /** @return SegmentEntity[] */
  public function findDynamicSegments(SubscriberEntity $subscriber): array {
    $segments = $this->segmentsRepository->findBy([
      'type' => SegmentEntity::TYPE_DYNAMIC,
    ]);

    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder()
      ->select('id')
      ->from($subscribersTable)
      ->where('id = :subscriberId')
      ->setParameter('subscriberId', $subscriber->getId());

    $matchingSegments = [];
    foreach ($segments as $segment) {
      $result = $this->filterHandler->apply(clone $queryBuilder, $segment)->execute();
      if ($result instanceof Result && $result->fetchOne()) {
        $matchingSegments[] = $segment;
      }
    }
    return $matchingSegments;
  }
}
