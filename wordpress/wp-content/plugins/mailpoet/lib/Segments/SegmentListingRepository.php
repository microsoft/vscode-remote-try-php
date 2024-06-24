<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Listing\ListingDefinition;
use MailPoet\Listing\ListingRepository;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

class SegmentListingRepository extends ListingRepository {
  const DEFAULT_SORT_BY = 'name';

  /** @var WooCommerce */
  private $wooCommerce;

  public function __construct(
    EntityManager $entityManager,
    WooCommerce $wooCommerce
  ) {
    parent::__construct($entityManager);
    $this->wooCommerce = $wooCommerce;
  }

  protected function applySelectClause(QueryBuilder $queryBuilder) {
    $queryBuilder->select("PARTIAL s.{id,name,type,description,createdAt,updatedAt,deletedAt,averageEngagementScore}");
  }

  protected function applyFromClause(QueryBuilder $queryBuilder) {
    $queryBuilder->from(SegmentEntity::class, 's');
  }

  protected function applyGroup(QueryBuilder $queryBuilder, string $group) {
    if ($group === 'trash') {
      $queryBuilder->andWhere('s.deletedAt IS NOT NULL');
    } else {
      $queryBuilder->andWhere('s.deletedAt IS NULL');
    }
  }

  protected function applySearch(QueryBuilder $queryBuilder, string $search) {
    $search = Helpers::escapeSearch($search);
    $queryBuilder
      ->andWhere('s.name LIKE :search or s.description LIKE :search')
      ->setParameter('search', "%$search%");
  }

  protected function applyFilters(QueryBuilder $queryBuilder, array $filters) {
  }

  protected function applyParameters(QueryBuilder $queryBuilder, array $parameters) {
    $types = [SegmentEntity::TYPE_DEFAULT, SegmentEntity::TYPE_WP_USERS];
    if ($this->wooCommerce->shouldShowWooCommerceSegment()) {
      $types[] = SegmentEntity::TYPE_WC_USERS;
    }
    $queryBuilder
      ->andWhere('s.type IN (:type)')
      ->setParameter('type', $types);
  }

  protected function applySorting(QueryBuilder $queryBuilder, string $sortBy, string $sortOrder) {
    if (!$sortBy) {
      $sortBy = self::DEFAULT_SORT_BY;
    }
    $queryBuilder->addOrderBy("s.$sortBy", $sortOrder);
  }

  public function getGroups(ListingDefinition $definition): array {
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);
    $this->applyParameters($queryBuilder, $definition->getParameters());

    $queryBuilder->select('count(s.id)');

    if (!$this->wooCommerce->shouldShowWooCommerceSegment()) {
      $queryBuilder
        ->andWhere('s.type != :wcUsers')
        ->setParameter('wcUsers', SegmentEntity::TYPE_WC_USERS);
    }

    $allQueryBuilder = clone $queryBuilder;
    $trashedQueryBuilder = clone $queryBuilder;

    $allQueryBuilder->andWhere('s.deletedAt IS NULL');
    $allCount = (int)$allQueryBuilder->getQuery()->getSingleScalarResult();

    $trashedQueryBuilder->andWhere('s.deletedAt IS NOT NULL');
    $trashedCount = (int)$trashedQueryBuilder->getQuery()->getSingleScalarResult();

    return [
      [
        'name' => 'all',
        'label' => __('All', 'mailpoet'),
        'count' => $allCount,
      ],
      [
        'name' => 'trash',
        'label' => __('Trash', 'mailpoet'),
        'count' => $trashedCount,
      ],
    ];
  }
}
