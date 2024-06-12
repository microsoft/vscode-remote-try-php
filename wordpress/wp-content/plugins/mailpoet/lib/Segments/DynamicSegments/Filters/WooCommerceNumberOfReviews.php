<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\DBCollationChecker;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class WooCommerceNumberOfReviews implements Filter {
  const ACTION = 'numberOfReviews';

  /** @var DBCollationChecker */
  private $collationChecker;

  /** @var FilterHelper */
  private $filterHelper;

  public function __construct(
    DBCollationChecker $collationChecker,
    FilterHelper $filterHelper
  ) {
    $this->collationChecker = $collationChecker;
    $this->filterHelper = $filterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $commentsTable = $this->filterHelper->getPrefixedTable('comments');
    $commentMetaTable = $this->filterHelper->getPrefixedTable('commentmeta');
    $filterData = $filter->getFilterData();
    $this->validateFilterData((array)$filterData->getData());
    /** @var string $type - for PHPStan because strval() doesn't accept a value of mixed */
    $type = $filterData->getParam('count_type');
    $type = strval($type);
    /** @var string $rating - for PHPStan because strval() doesn't accept a value of mixed */
    $rating = $filterData->getParam('rating');
    $rating = strval($rating);
    /** @var int $days - for PHPStan because intval() doesn't accept a value of mixed */
    $days = $filterData->getParam('days');
    $days = intval($days);
    /** @var int $count - for PHPStan because intval() doesn't accept a value of mixed */
    $count = $filterData->getParam('count');
    $count = intval($count);

    $subscribersTable = $this->filterHelper->getSubscribersTable();
    $collation = $this->collationChecker->getCollateIfNeeded(
      $subscribersTable,
      'email',
      $commentsTable,
      'comment_author_email'
    );

    $isAllTime = $filterData->getParam('timeframe') === DynamicSegmentFilterData::TIMEFRAME_ALL_TIME;
    $joinCondition = "$subscribersTable.email = comments.comment_author_email $collation
      AND comments.comment_type = 'review'";

    if (!$isAllTime) {
      $date = Carbon::now()->subDays($days);
      $dateParam = $this->filterHelper->getUniqueParameterName('date');
      $joinCondition .= " AND comments.comment_date >= :$dateParam";
      $queryBuilder->setParameter($dateParam, $date->toDateTimeString());
    }

    $commentMetaJoinCondition = "comments.comment_ID = commentmeta.comment_id AND commentmeta.meta_key = 'rating'";

    if ($rating !== 'any') {
      $ratingParam = $this->filterHelper->getUniqueParameterName('rating');
      $commentMetaJoinCondition .= "AND commentmeta.meta_value = :$ratingParam";
      $queryBuilder->setParameter($ratingParam, $rating);
    }

    $queryBuilder
      ->leftJoin(
        $subscribersTable,
        $commentsTable,
        'comments',
        $joinCondition
      )->leftJoin(
        'comments',
        $commentMetaTable,
        'commentmeta',
        $commentMetaJoinCondition
      );

    $queryBuilder->groupBy('inner_subscriber_id');

    $countParam = $this->filterHelper->getUniqueParameterName('count');

    switch ($type) {
      case '=':
        $queryBuilder->having("COUNT(commentmeta.meta_value) = :$countParam");
        break;
      case '!=':
        $queryBuilder->having("COUNT(commentmeta.meta_value) != :$countParam");
        break;
      case '>':
        $queryBuilder->having("COUNT(commentmeta.meta_value) > :$countParam");
        break;
      case '<':
        $queryBuilder->having("COUNT(commentmeta.meta_value) < :$countParam");
        break;
    }

    $queryBuilder->setParameter($countParam, $count, 'integer');
    return $queryBuilder;
  }

  public function validateFilterData(array $data): void {
    if (!isset($data['rating'])) {
      throw new InvalidFilterException('Missing rating', InvalidFilterException::MISSING_VALUE);
    }
    $validRatings = ['1', '2', '3', '4', '5', 'any'];
    if (!in_array($data['rating'], $validRatings, true)) {
      throw new InvalidFilterException('Invalid rating', InvalidFilterException::MISSING_VALUE);
    }
    if (!isset($data['count_type'])) {
      throw new InvalidFilterException('Missing count type', InvalidFilterException::MISSING_VALUE);
    }
    $type = $data['count_type'];
    $validTypes = [
      '=',
      '!=',
      '>',
      '<',
    ];
    if (!in_array($type, $validTypes, true)) {
      throw new InvalidFilterException('Invalid count type', InvalidFilterException::INVALID_TYPE);
    }

    if (!isset($data['count'])) {
      throw new InvalidFilterException('Missing review count', InvalidFilterException::MISSING_VALUE);
    }
    $this->filterHelper->validateDaysPeriodData($data);
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
