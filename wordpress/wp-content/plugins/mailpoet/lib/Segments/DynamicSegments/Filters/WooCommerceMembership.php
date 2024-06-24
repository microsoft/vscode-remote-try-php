<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class WooCommerceMembership implements Filter {
  const ACTION_MEMBER_OF = 'isMemberOf';

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    /** @var array */
    $planIds = $filterData->getParam('plan_ids');
    $operator = $filterData->getParam('operator');
    $parameterSuffix = $filter->getId() ?: Security::generateRandomString();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    // ALL OF
    if ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $this->applyPostJoin($queryBuilder);
      $this->applyParentPostJoin($queryBuilder);
      return $queryBuilder
        ->andWhere("posts.post_parent IN (:plans" . $parameterSuffix . ")")
        ->groupBy("$subscribersTable.id")
        ->having("COUNT($subscribersTable.id) = :count$parameterSuffix")
        ->setParameter('plans' . $parameterSuffix, $planIds, Connection::PARAM_STR_ARRAY)
        ->setParameter('count' . $parameterSuffix, count($planIds));
    }

    // NONE OF
    if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $subQueryBuilder = $this->entityManager->getConnection()
        ->createQueryBuilder()
        ->from($subscribersTable)
        ->select("DISTINCT $subscribersTable.id");
      $this->applyPostJoin($subQueryBuilder);
      $this->applyParentPostJoin($subQueryBuilder);
      $subQueryBuilder
        ->andWhere("posts.post_parent IN (:plans" . $parameterSuffix . ")");
      return $queryBuilder->where("{$subscribersTable}.id NOT IN ({$subQueryBuilder->getSQL()})")
        ->setParameter('plans' . $parameterSuffix, $planIds, Connection::PARAM_STR_ARRAY);
    }

    // ANY
    $this->applyPostJoin($queryBuilder);
    $this->applyParentPostJoin($queryBuilder);
    return $queryBuilder
      ->andWhere("posts.post_parent IN (:plans" . $parameterSuffix . ")")
      ->setParameter('plans' . $parameterSuffix, $planIds, Connection::PARAM_STR_ARRAY);
  }

  private function applyPostJoin(QueryBuilder $queryBuilder): QueryBuilder {
    global $wpdb;
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    return $queryBuilder->innerJoin(
      $subscribersTable,
      $wpdb->posts,
      'posts',
      "posts.post_type = 'wc_user_membership' AND posts.post_status IN ('wcm-active', 'wcm-complimentary', 'wcm-free_trial', 'wcm-pending') AND posts.post_author=$subscribersTable.wp_user_id"
    );
  }

  private function applyParentPostJoin(QueryBuilder $queryBuilder): QueryBuilder {
    global $wpdb;
    return $queryBuilder->innerJoin(
      'posts',
      $wpdb->posts,
      'parentposts',
      'posts.post_parent = parentposts.id AND parentposts.post_type = "wc_membership_plan" AND parentposts.post_status = "publish"'
    );
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
