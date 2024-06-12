<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class FilterHelper {
  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
  }

  public function getPrefixedTable(string $table): string {
    global $wpdb;
    return sprintf('%s%s', $wpdb->prefix, $table);
  }

  public function getNewSubscribersQueryBuilder(): QueryBuilder {
    return $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select($this->getSubscribersTable() . '.id')
      ->from($this->getSubscribersTable());
  }

  public function getSubscribersTable(): string {
    return $this->getTableForEntity(SubscriberEntity::class);
  }

  /**
   * @param class-string<object> $entityClass
   */
  public function getTableForEntity(string $entityClass): string {
    return $this->entityManager->getClassMetadata($entityClass)->getTableName();
  }

  public function getInterpolatedSQL(QueryBuilder $query): string {
    $sql = $query->getSQL();
    $params = $query->getParameters();
    $search = array_map(function($key) {
      return ":$key";
    }, array_keys($params));
    $replace = array_map(function($value) use ($query) {
      if (is_array($value)) {
        $quotedValues = array_map(function($arrayValue) use ($query) {
          return $query->expr()->literal($arrayValue);
        }, $value);
        return implode(',', $quotedValues);
      }
      return $query->expr()->literal($value);
    }, array_values($params));
    return str_replace($search, $replace, $sql);
  }

  public function getUniqueParameterName(string $parameter): string {
    $suffix = Security::generateRandomString();
    return sprintf("%s_%s", $parameter, $suffix);
  }

  public function validateDaysPeriodData(array $data): void {
    if (!isset($data['timeframe']) || !in_array($data['timeframe'], [DynamicSegmentFilterData::TIMEFRAME_ALL_TIME, DynamicSegmentFilterData::TIMEFRAME_IN_THE_LAST], true)) {
      throw new InvalidFilterException('Missing timeframe type', InvalidFilterException::MISSING_VALUE);
    }

    if ($data['timeframe'] === DynamicSegmentFilterData::TIMEFRAME_ALL_TIME) {
      return;
    }

    $days = intval($data['days'] ?? null);

    if ($days < 1) {
      throw new InvalidFilterException('Missing number of days', InvalidFilterException::MISSING_VALUE);
    }
  }
}
