<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoetVendor\Carbon\Carbon;

/**
 * @extends Repository<StatisticsUnsubscribeEntity>
 */
class StatisticsUnsubscribesRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsUnsubscribeEntity::class;
  }

  public function getTotalForMonths(int $forMonths): int {
    $from = (new Carbon())->subMonths($forMonths);
    $count = $this->entityManager->createQueryBuilder()
      ->select('count(stats.id)')
      ->from(StatisticsUnsubscribeEntity::class, 'stats')
      ->andWhere('stats.createdAt >= :dateTime')
      ->setParameter('dateTime', $from)
      ->getQuery()
      ->getSingleScalarResult();

    return intval($count);
  }

  public function getCountPerMethodForMonths(int $forMonths): array {
    $from = (new Carbon())->subMonths($forMonths);
    return $this->entityManager->createQueryBuilder()
      ->select('count(stats.id) as count, stats.method as method')
      ->from(StatisticsUnsubscribeEntity::class, 'stats')
      ->andWhere('stats.createdAt >= :dateTime')
      ->groupBy('stats.method')
      ->setParameter('dateTime', $from)
      ->getQuery()
      ->getResult();
  }
}
