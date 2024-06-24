<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoetVendor\Doctrine\ORM\EntityManager;

/**
 * @extends Repository<DynamicSegmentFilterEntity>
 */
class DynamicSegmentFilterRepository extends Repository {
  public function __construct(
    EntityManager $entityManager
  ) {
    parent::__construct($entityManager);
  }

  protected function getEntityClassName() {
    return DynamicSegmentFilterEntity::class;
  }

  public function findOnyByFilterTypeAndAction(string $filterType, string $action): ?DynamicSegmentFilterEntity {
    return $this->entityManager->createQueryBuilder()
      ->select('dsf')
      ->from(DynamicSegmentFilterEntity::class, 'dsf')
      ->where('dsf.filterData.filterType = :filterType')
      ->andWhere('dsf.filterData.action = :action')
      ->setParameter('filterType', $filterType)
      ->setParameter('action', $action)
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }
}
