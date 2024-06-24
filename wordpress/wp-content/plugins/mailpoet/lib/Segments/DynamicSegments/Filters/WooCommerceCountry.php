<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Util\DBCollationChecker;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class WooCommerceCountry implements Filter {
  const ACTION_CUSTOMER_COUNTRY = 'customerInCountry';

  /** @var EntityManager */
  private $entityManager;

  /** @var DBCollationChecker */
  private $collationChecker;

  public function __construct(
    EntityManager $entityManager,
    DBCollationChecker $collationChecker
  ) {
    $this->entityManager = $entityManager;
    $this->collationChecker = $collationChecker;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    global $wpdb;
    $filterData = $filter->getFilterData();
    $countryCode = $filterData->getParam('country_code');
    if (!is_array($countryCode)) {
      $countryCode = [(string)$countryCode];
    }
    $operator = $filterData->getParam('operator');
    if (!$operator) {
      $operator = DynamicSegmentFilterData::OPERATOR_ANY;
    }

    $countryFilterParam = ((string)$filter->getId()) . Security::generateRandomString();
    $condition = $this->createCondition($countryCode, $operator, $countryFilterParam);
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $collation = $this->collationChecker->getCollateIfNeeded(
      $subscribersTable,
      'email',
      $wpdb->prefix . 'wc_customer_lookup',
      'email'
    );
    $qb = $queryBuilder->innerJoin(
      $subscribersTable,
      $wpdb->prefix . 'wc_customer_lookup',
      'customer',
      "$subscribersTable.email = customer.email $collation"
    )->where($condition);

    foreach ($countryCode as $key => $userCountryCode) {
      $qb->setParameter('countryCode' . $key . $countryFilterParam, '%' . $userCountryCode . '%');
    }

    return $qb;
  }

  private function createCondition(array $countryCodes, string $operator, string $countryFilterParam): string {
    $sqlParts = [];
    foreach ($countryCodes as $key => $userCountryCode) {
      if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
        $sqlParts[] = '(customer.country NOT LIKE :countryCode' . $key . $countryFilterParam . ')';
      } else {
        $sqlParts[] = '(customer.country LIKE :countryCode' . $key . $countryFilterParam . ')';
      }
    }
    if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      return join(' AND ', $sqlParts);
    }
    return join(' OR ', $sqlParts);
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
