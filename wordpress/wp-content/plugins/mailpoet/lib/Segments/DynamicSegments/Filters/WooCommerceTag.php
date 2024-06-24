<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use WP_Term;

class WooCommerceTag implements Filter {
  const ACTION = 'purchasedTag';

  private WPFunctions $wp;
  private WooFilterHelper $wooFilterHelper;
  private FilterHelper $filterHelper;

  public function __construct(
    FilterHelper $filterHelper,
    WooFilterHelper $wooFilterHelper,
    WPFunctions $wp
  ) {
    $this->wp = $wp;
    $this->wooFilterHelper = $wooFilterHelper;
    $this->filterHelper = $filterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $this->validateFilterData((array)$filterData->getData());

    $operator = $filterData->getOperator();

    if ($operator === DynamicSegmentFilterData::OPERATOR_ANY) {
      $this->applyForAnyOperator($queryBuilder, $filterData);
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $this->applyForAnyOperator($queryBuilder, $filterData);
      $countParam = $this->filterHelper->getUniqueParameterName('tagCount');
      $queryBuilder->groupBy('inner_subscriber_id')
        ->having("COUNT(DISTINCT term_taxonomy.term_id) = :$countParam")
        ->setParameter($countParam, count($filterData->getArrayParam('tag_ids')));
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $subQuery = $this->filterHelper->getNewSubscribersQueryBuilder();
      $this->applyForAnyOperator($subQuery, $filterData);
      $subscribersTable = $this->filterHelper->getSubscribersTable();
      $queryBuilder->andWhere($queryBuilder->expr()->notIn("$subscribersTable.id", $this->filterHelper->getInterpolatedSQL($subQuery)));
    }

    return $queryBuilder;
  }

  public function applyForAnyOperator(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData): void {
    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder);
    $tagIdsParam = $this->filterHelper->getUniqueParameterName('tagIds');
    $productAlias = $this->applyProductJoin($queryBuilder, $orderStatsAlias);
    $queryBuilder->join(
      $productAlias,
      $this->filterHelper->getPrefixedTable('term_relationships'),
      'term_relationships',
      'product.product_id = term_relationships.object_id'
    );
    $queryBuilder->innerJoin(
      'term_relationships',
      $this->filterHelper->getPrefixedTable('term_taxonomy'),
      'term_taxonomy',
      "term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
      AND
      term_taxonomy.term_id IN (:$tagIdsParam)"
    );
    $queryBuilder->setParameter($tagIdsParam, $filterData->getArrayParam('tag_ids'), Connection::PARAM_STR_ARRAY);
  }

  private function applyProductJoin(QueryBuilder $queryBuilder, string $orderStatsAlias, string $productAlias = 'product'): string {
    $queryBuilder->innerJoin(
      $orderStatsAlias,
      $this->filterHelper->getPrefixedTable('wc_order_product_lookup'),
      $productAlias,
      "$orderStatsAlias.order_id = product.order_id"
    );
    return $productAlias;
  }

  public function validateFilterData(array $data): void {
    $operator = $data['operator'] ?? null;

    if (
      !in_array($operator, [
        DynamicSegmentFilterData::OPERATOR_ANY,
        DynamicSegmentFilterData::OPERATOR_ALL,
        DynamicSegmentFilterData::OPERATOR_NONE,
      ])
    ) {
      throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
    }

    if (!is_array($data['tag_ids'] ?? null) || count($data['tag_ids']) === 0) {
      throw new InvalidFilterException('Missing tag ids');
    }
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $lookupData = [
      'tags' => [],
    ];
    $tagIds = $filterData->getArrayParam('tag_ids');
    $terms = $this->wp->getTerms('product_tag', ['include' => $tagIds, 'hide_empty' => false]);
    /** @var WP_Term[] $terms */
    foreach ($terms as $term) {
      $lookupData['tags'][$term->term_id] = $term->name; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }

    return $lookupData;
  }
}
