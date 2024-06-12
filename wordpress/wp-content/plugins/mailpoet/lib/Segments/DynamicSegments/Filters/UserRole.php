<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class UserRole implements Filter {
  const TYPE = 'wordpressRole';

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    global $wpdb;
    $filterData = $filter->getFilterData();
    $role = $filterData->getParam('wordpressRole');
    $operator = $filterData->getParam('operator');
    if (!$role) {
      throw new InvalidFilterException('Missing role', InvalidFilterException::MISSING_ROLE);
    }
    if (!is_array($role)) {
      // compatibility with the older segment before multiple roles were added
      $role = [$role];
    }
    if (!$operator) {
      $operator = DynamicSegmentFilterData::OPERATOR_ANY;
    }

    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $parameterSuffix = ((string)$filter->getId()) . Security::generateRandomString();
    $condition = $this->createCondition($role, $operator, $parameterSuffix);
    $qb = $queryBuilder->join($subscribersTable, $wpdb->users, 'wpusers', "$subscribersTable.wp_user_id = wpusers.id")
      ->join('wpusers', $wpdb->usermeta, 'wpusermeta', 'wpusers.id = wpusermeta.user_id')
      ->andWhere("wpusermeta.meta_key = '{$wpdb->prefix}capabilities' AND (" . $condition . ')');
    foreach ($role as $key => $userRole) {
      $qb->setParameter(':role' . $key . $parameterSuffix, '%"' . $userRole . '"%');
    }
    return $qb;
  }

  /**
   * @param string[] $roles
   * @param string $operator
   * @param string $parameterSuffix
   * @return string
   */
  private function createCondition(array $roles, string $operator, $parameterSuffix): string {
    $sqlParts = [];
    foreach ($roles as $key => $role) {
      if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
        $sqlParts[] = '(wpusermeta.meta_value NOT LIKE :role' . $key . $parameterSuffix . ')';
      } else {
        $sqlParts[] = '(wpusermeta.meta_value LIKE :role' . $key . $parameterSuffix . ')';
      }
    }
    if (($operator === DynamicSegmentFilterData::OPERATOR_NONE) || ($operator === DynamicSegmentFilterData::OPERATOR_ALL)) {
      return join(' AND ', $sqlParts);
    }
    return join(' OR ', $sqlParts);
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    global $wp_roles;
    $lookupData = [
      'roles' => [],
    ];
    $roles = $filterData->getParam('wordpressRole');
    if (is_string($roles)) {
      $roles = [$roles];
    }
    if (!is_array($roles)) {
      throw new InvalidStateException();
    }
    foreach ($roles as $roleSlug) {
      $roleData = $wp_roles->roles[$roleSlug] ?? null;
      if (is_array($roleData)) {
        $lookupData['roles'][$roleSlug] = $roleData['name'];
      }
    }
    return $lookupData;
  }
}
