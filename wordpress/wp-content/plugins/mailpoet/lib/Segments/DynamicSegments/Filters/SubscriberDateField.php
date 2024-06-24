<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class SubscriberDateField implements Filter {
  const LAST_CLICK_DATE = 'lastClickDate';
  const LAST_ENGAGEMENT_DATE = 'lastEngagementDate';
  const LAST_PURCHASE_DATE = 'lastPurchaseDate';
  const LAST_OPEN_DATE = 'lastOpenDate';
  const LAST_PAGE_VIEW_DATE = 'lastPageViewDate';
  const LAST_SENDING_DATE = 'lastSendingDate';

  // Slightly different naming due to backwards compatibility
  const SUBSCRIBED_DATE = 'subscribedDate';

  const TYPES = [
    self::LAST_CLICK_DATE,
    self::LAST_ENGAGEMENT_DATE,
    self::LAST_PURCHASE_DATE,
    self::LAST_OPEN_DATE,
    self::LAST_PAGE_VIEW_DATE,
    self::LAST_SENDING_DATE,
    self::SUBSCRIBED_DATE,
  ];

  /** @var DateFilterHelper */
  private $dateFilterHelper;

  /** @var FilterHelper */
  private $filterHelper;

  public function __construct(
    FilterHelper $filterHelper,
    DateFilterHelper $dateFilterHelper
  ) {
    $this->filterHelper = $filterHelper;
    $this->dateFilterHelper = $dateFilterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $operator = $this->dateFilterHelper->getOperatorFromFilter($filter);
    $action = $filter->getFilterData()->getAction();
    $value = $this->dateFilterHelper->getDateValueFromFilter($filter);
    $parameter = $this->filterHelper->getUniqueParameterName('date');
    $date = $this->dateFilterHelper->getDateStringForOperator($operator, $value);

    if (!is_string($action)) {
      throw new InvalidFilterException('Missing action', InvalidFilterException::MISSING_ACTION);
    }

    $columnName = $this->getColumnNameForAction($action);

    switch ($operator) {
      case DateFilterHelper::BEFORE:
      case DateFilterHelper::NOT_IN_THE_LAST:
        $queryBuilder->andWhere("DATE($columnName) < :$parameter");
        break;
      case DateFilterHelper::AFTER:
        $queryBuilder->andWhere("DATE($columnName) > :$parameter");
        break;
      case DateFilterHelper::ON:
        $queryBuilder->andWhere("DATE($columnName) = :$parameter");
        break;
      case DateFilterHelper::ON_OR_BEFORE:
        $queryBuilder->andWhere("DATE($columnName) <= :$parameter");
        break;
      case DateFilterHelper::NOT_ON:
        $queryBuilder->andWhere("DATE($columnName) != :$parameter");
        break;
      case DateFilterHelper::IN_THE_LAST:
      case DateFilterHelper::ON_OR_AFTER:
        $queryBuilder->andWhere("DATE($columnName) >= :$parameter");
        break;
      default:
        throw new InvalidFilterException('Incorrect value for operator', InvalidFilterException::MISSING_VALUE);
    }
    $queryBuilder->setParameter($parameter, $date);

    return $queryBuilder;
  }

  public function getColumnNameForAction(string $action): string {
    switch ($action) {
      case self::LAST_CLICK_DATE:
        return 'last_click_at';
      case self::LAST_ENGAGEMENT_DATE:
        return 'last_engagement_at';
      case self::LAST_PURCHASE_DATE:
        return 'last_purchase_at';
      case self::LAST_OPEN_DATE:
        return 'last_open_at';
      case self::LAST_PAGE_VIEW_DATE:
        return 'last_page_view_at';
      case self::SUBSCRIBED_DATE:
        return 'last_subscribed_at';
      case self::LAST_SENDING_DATE:
        return 'last_sending_at';
      default:
        throw new InvalidFilterException('Invalid action', InvalidFilterException::MISSING_ACTION);
    }
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
