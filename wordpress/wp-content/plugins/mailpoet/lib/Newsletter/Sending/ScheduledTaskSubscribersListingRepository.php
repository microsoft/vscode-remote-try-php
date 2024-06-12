<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Sending;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Listing\ListingDefinition;
use MailPoet\Listing\ListingRepository;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

class ScheduledTaskSubscribersListingRepository extends ListingRepository {
  public function getGroups(ListingDefinition $definition): array {
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);
    $this->applyParameters($queryBuilder, $definition->getParameters());

    // total count
    $countQueryBuilder = clone $queryBuilder;
    $countQueryBuilder->select('COUNT(sts.subscriber) AS subscriberCount');
    $totalCount = intval($countQueryBuilder->getQuery()->getSingleScalarResult());

    // Sent count
    $sentCountQuery = clone $queryBuilder;
    $sentCountQuery->select('COUNT(sts.subscriber) AS subscriberCount');
    $sentCountQuery->andWhere('sts.processed = :processedStatus');
    $sentCountQuery->andWhere('sts.failed = :failedStatus');
    $sentCountQuery->setParameter('processedStatus', ScheduledTaskSubscriberEntity::STATUS_PROCESSED);
    $sentCountQuery->setParameter('failedStatus', ScheduledTaskSubscriberEntity::FAIL_STATUS_OK);
    $sentCount = intval($sentCountQuery->getQuery()->getSingleScalarResult());

    // Failed count
    $failedCountQuery = clone $queryBuilder;
    $failedCountQuery->select('COUNT(sts.subscriber) AS subscriberCount');
    $failedCountQuery->andWhere('sts.failed = :failedStatus');
    $failedCountQuery->setParameter('failedStatus', ScheduledTaskSubscriberEntity::FAIL_STATUS_FAILED);
    $failedCount = intval($failedCountQuery->getQuery()->getSingleScalarResult());

    // Unprocessed count
    $unprocessedCountQuery = clone $queryBuilder;
    $unprocessedCountQuery->select('COUNT(sts.subscriber) AS subscriberCount');
    $unprocessedCountQuery->andWhere('sts.processed = :processedStatus');
    $unprocessedCountQuery->setParameter('processedStatus', ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED);
    $unprocessedCount = intval($unprocessedCountQuery->getQuery()->getSingleScalarResult());

    return [
      [
        'name' => 'all',
        'label' => __('All', 'mailpoet'),
        'count' => $totalCount,
      ],
      [
        'name' => ScheduledTaskSubscriberEntity::SENDING_STATUS_SENT,
        'label' => __('Sent', 'mailpoet'),
        'count' => $sentCount,
      ],
      [
        'name' => ScheduledTaskSubscriberEntity::SENDING_STATUS_FAILED,
        'label' => __('Failed', 'mailpoet'),
        'count' => $failedCount,
      ],
      [
        'name' => ScheduledTaskSubscriberEntity::SENDING_STATUS_UNPROCESSED,
        'label' => __('Unprocessed', 'mailpoet'),
        'count' => $unprocessedCount,
      ],
    ];
  }

  protected function applySelectClause(QueryBuilder $queryBuilder) {
    $queryBuilder->select("PARTIAL sts.{task,subscriber,processed,failed,error,createdAt,updatedAt}, PARTIAL s.{id, email, firstName, lastName}");
  }

  protected function applyFromClause(QueryBuilder $queryBuilder) {
    $queryBuilder->from(ScheduledTaskSubscriberEntity::class, 'sts')
      ->leftJoin('sts.subscriber', 's');
  }

  protected function applyGroup(QueryBuilder $queryBuilder, string $group) {
    if ($group === ScheduledTaskSubscriberEntity::SENDING_STATUS_SENT) {
      $queryBuilder->andWhere('sts.processed = :processedStatus');
      $queryBuilder->andWhere('sts.failed = :failedStatus');
      $queryBuilder->setParameter('processedStatus', ScheduledTaskSubscriberEntity::STATUS_PROCESSED);
      $queryBuilder->setParameter('failedStatus', ScheduledTaskSubscriberEntity::FAIL_STATUS_OK);
    } elseif ($group === ScheduledTaskSubscriberEntity::SENDING_STATUS_FAILED) {
      $queryBuilder->andWhere('sts.failed = :failedStatus');
      $queryBuilder->setParameter('failedStatus', ScheduledTaskSubscriberEntity::FAIL_STATUS_FAILED);
    } elseif ($group === ScheduledTaskSubscriberEntity::SENDING_STATUS_UNPROCESSED) {
      $queryBuilder->andWhere('sts.processed = :processedStatus');
      $queryBuilder->setParameter('processedStatus', ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED);
    }
  }

  protected function applySorting(QueryBuilder $queryBuilder, string $sortBy, string $sortOrder) {
    // ScheduledTaskSubscriber doesn't have id column so the default fallback value 'id'
    // generated in MailPoet\Listing\Handler needs to be changed to something else
    if ($sortBy === 'id') {
      $sortBy = 'sts.subscriber';
    } elseif ($sortBy === 'subscriberId') { // Ordering by subscriberId is mapped to email for consistency with Subscriber listing
      $sortBy = 's.email';
    } else {
      $sortBy = "sts.{$sortBy}";
    }
    $queryBuilder->addOrderBy($sortBy, $sortOrder);
  }

  protected function applySearch(QueryBuilder $queryBuilder, string $search) {
    $search = Helpers::escapeSearch($search);
    $queryBuilder
      ->andWhere('s.email LIKE :search or s.firstName LIKE :search or s.lastName LIKE :search')
      ->setParameter('search', "%$search%");
  }

  protected function applyFilters(QueryBuilder $queryBuilder, array $filters) {
    // the parent class requires this method, but scheduled task subscribers listing doesn't currently support this feature.
  }

  protected function applyParameters(QueryBuilder $queryBuilder, array $parameters) {
    if (isset($parameters['task_ids']) && !empty($parameters['task_ids'])) {
      $queryBuilder->andWhere('sts.task IN (:taskIds)')
        ->setParameter('taskIds', $parameters['task_ids']);
    }
  }

  public function getCount(ListingDefinition $definition): int {
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);
    $this->applyConstraints($queryBuilder, $definition);
    $queryBuilder->select("COUNT(DISTINCT sts.subscriber)");
    return intval($queryBuilder->getQuery()->getSingleScalarResult());
  }
}
