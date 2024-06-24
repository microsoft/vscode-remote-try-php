<?php declare(strict_types = 1);

namespace MailPoet\Listing;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

abstract class ListingRepository {
  /** @var QueryBuilder */
  protected $queryBuilder;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->queryBuilder = $entityManager->createQueryBuilder();
  }

  public function getData(ListingDefinition $definition): array {
    $queryBuilder = clone $this->queryBuilder;
    $sortBy = Helpers::underscoreToCamelCase($definition->getSortBy());
    $this->applySelectClause($queryBuilder);
    $this->applyFromClause($queryBuilder);
    $this->applyConstraints($queryBuilder, $definition);
    $this->applySorting($queryBuilder, $sortBy, $definition->getSortOrder());
    $this->applyPaging($queryBuilder, $definition->getOffset(), $definition->getLimit());
    return $queryBuilder->getQuery()->getResult();
  }

  public function getCount(ListingDefinition $definition): int {
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);
    $this->applyConstraints($queryBuilder, $definition);
    $alias = $queryBuilder->getRootAliases()[0];
    $queryBuilder->select("COUNT(DISTINCT $alias)");
    return (int)$queryBuilder->getQuery()->getSingleScalarResult();
  }

  public function getActionableIds(ListingDefinition $definition): array {
    $ids = $definition->getSelection();
    if (!empty($ids)) {
      return $ids;
    }
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);
    $this->applyConstraints($queryBuilder, $definition);
    $alias = $queryBuilder->getRootAliases()[0];
    $queryBuilder->select("$alias.id");
    $ids = $queryBuilder->getQuery()->getScalarResult();
    return array_column($ids, 'id');
  }

  public function getGroups(ListingDefinition $definition): array {
    return [];
  }

  public function getFilters(ListingDefinition $definition): array {
    return [];
  }

  abstract protected function applySelectClause(QueryBuilder $queryBuilder);

  abstract protected function applyFromClause(QueryBuilder $queryBuilder);

  protected function applyConstraints(QueryBuilder $queryBuilder, ListingDefinition $definition) {
    $group = $definition->getGroup();
    if ($group) {
      $this->applyGroup($queryBuilder, $group);
    }

    $search = $definition->getSearch();
    if ($search && strlen(trim($search)) > 0) {
      $this->applySearch($queryBuilder, $search);
    }

    $filters = $definition->getFilters();
    if ($filters) {
      $this->applyFilters($queryBuilder, $filters);
    }

    $parameters = $definition->getParameters();
    if ($parameters) {
      $this->applyParameters($queryBuilder, $parameters);
    }
  }

  abstract protected function applyGroup(QueryBuilder $queryBuilder, string $group);

  abstract protected function applySearch(QueryBuilder $queryBuilder, string $search);

  abstract protected function applyFilters(QueryBuilder $queryBuilder, array $filters);

  abstract protected function applyParameters(QueryBuilder $queryBuilder, array $parameters);

  protected function applySorting(QueryBuilder $queryBuilder, string $sortBy, string $sortOrder) {
    $alias = $this->queryBuilder->getRootAliases()[0];
    $queryBuilder->addOrderBy("$alias.$sortBy", $sortOrder);
  }

  protected function applyPaging(QueryBuilder $queryBuilder, int $offset, int $limit) {
    $queryBuilder->setFirstResult($offset);
    $queryBuilder->setMaxResults($limit);
  }
}
