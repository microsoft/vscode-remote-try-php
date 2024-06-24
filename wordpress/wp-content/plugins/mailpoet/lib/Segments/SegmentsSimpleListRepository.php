<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\SubscribersCountsController;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SegmentsSimpleListRepository {
  /** @var EntityManager */
  private $entityManager;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  public function __construct(
    EntityManager $entityManager,
    SubscribersCountsController $subscribersCountsController
  ) {
    $this->entityManager = $entityManager;
    $this->subscribersCountsController = $subscribersCountsController;
  }

  /**
   * This fetches list of all segments basic data and count of subscribed subscribers.
   * @return array<array{id: string, name: string, type: string, subscribers: int}>
   */
  public function getListWithSubscribedSubscribersCounts(array $segmentTypes = []): array {
    return $this->getList(
      $segmentTypes,
      SubscriberEntity::STATUS_SUBSCRIBED
    );
  }

  /**
   * This fetches list of all segments basic data and count of subscribers associated to a segment regardless their subscription status.
   * @return array<array{id: string, name: string, type: string, subscribers: int}>
   */
  public function getListWithAssociatedSubscribersCounts(array $segmentTypes = []): array {
    return $this->getList(
      $segmentTypes
    );
  }

  /**
   * Adds a virtual segment with for subscribers without list
   * @return array<array{id: string, name: string, type: string, subscribers: int}>
   */
  public function addVirtualSubscribersWithoutListSegment(array $segments): array {
    $withoutSegmentStats = $this->subscribersCountsController->getSubscribersWithoutSegmentStatisticsCount();
    $segments[] = [
      'id' => '0',
      'type' => SegmentEntity::TYPE_WITHOUT_LIST,
      'name' => __('Subscribers without a list', 'mailpoet'),
      'subscribers' => $withoutSegmentStats['all'],
    ];
    return $segments;
  }

  /**
   * @return array<array{id: string, name: string, type: string, subscribers: int}>
   */
  private function getList(
    array $segmentTypes = [],
    string $subscriberGlobalStatus = null
  ): array {
    $segmentsTable = $this->entityManager->getClassMetadata(SegmentEntity::class)->getTableName();

    $segmentsDataQuery = $this->entityManager
      ->getConnection()
      ->createQueryBuilder();

    $segmentsDataQuery->select(
      "segments.id, segments.name, segments.type"
    )->from($segmentsTable, 'segments')
      ->where('segments.deleted_at IS NULL')
      ->orderBy('segments.name');

    if (!empty($segmentTypes)) {
      $segmentsDataQuery
        ->andWhere('segments.type IN (:typesParam)')
        ->setParameter('typesParam', $segmentTypes, Connection::PARAM_STR_ARRAY);
    }

    $result = $segmentsDataQuery->executeQuery();
    if (!$result instanceof Result) {
      return [];
    }
    $segments = $result->fetchAll();

    // Fetch subscribers counts for static and dynamic segments and correct data types
    foreach ($segments as $key => $segment) {
      // BC compatibility fix. PHP8.1+ returns integer but JS apps expect string
      $segments[$key]['id'] = (string)$segment['id'];
      $statisticsKey = $subscriberGlobalStatus ?: 'all';
      $segments[$key]['subscribers'] = (int)$this->subscribersCountsController->getSegmentStatisticsCountById($segment['id'])[$statisticsKey];
    }
    /* @var array<array{id: string, name: string, type: string, subscribers: int}> */
    return $segments;
  }
}
