<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\WP\Functions;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class WooCommercePurchasedWithAttribute implements Filter {
  const ACTION = 'purchasedWithAttribute';

  const TYPE_LOCAL = 'local';
  const TYPE_TAXONOMY = 'taxonomy';

  private WooFilterHelper $wooFilterHelper;

  private FilterHelper $filterHelper;

  private Functions $wp;

  public function __construct(
    FilterHelper $filterHelper,
    WooFilterHelper $wooFilterHelper,
    Functions $wp
  ) {
    $this->wooFilterHelper = $wooFilterHelper;
    $this->filterHelper = $filterHelper;
    $this->wp = $wp;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $this->validateFilterData((array)$filterData->getData());

    $type = $filterData->getStringParam('attribute_type');

    if ($type === self::TYPE_LOCAL) {
      $this->applyForLocalAttribute($queryBuilder, $filterData);
    } elseif ($type === self::TYPE_TAXONOMY) {
      $this->applyForTaxonomyAttribute($queryBuilder, $filterData);
    }

    return $queryBuilder;
  }

  private function applyForTaxonomyAnyOperator(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData): void {
    $attributeTaxonomySlug = $filterData->getStringParam('attribute_taxonomy_slug');
    $attributeTermIds = $filterData->getArrayParam('attribute_term_ids');
    $termIdsParam = $this->filterHelper->getUniqueParameterName('attribute_term_ids');
    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder);
    $productAlias = $this->applyProductJoin($queryBuilder, $orderStatsAlias);
    $attributeAlias = $this->applyTaxonomyAttributeJoin($queryBuilder, $productAlias, $attributeTaxonomySlug);
    $queryBuilder->andWhere("$attributeAlias.term_id IN (:$termIdsParam)");
    $queryBuilder->setParameter($termIdsParam, $attributeTermIds, Connection::PARAM_STR_ARRAY);
  }

  private function applyProductJoin(QueryBuilder $queryBuilder, string $orderStatsAlias, string $alias = 'product'): string {
    $queryBuilder->innerJoin(
      $orderStatsAlias,
      $this->filterHelper->getPrefixedTable('wc_order_product_lookup'),
      $alias,
      "$orderStatsAlias.order_id = product.order_id"
    );
    return $alias;
  }

  private function applyTaxonomyAttributeJoin(QueryBuilder $queryBuilder, string $productAlias, $taxonomySlug, string $alias = 'attribute'): string {
    $queryBuilder->innerJoin(
      $productAlias,
      $this->filterHelper->getPrefixedTable('wc_product_attributes_lookup'),
      $alias,
      "product.product_id = attribute.product_id AND attribute.taxonomy = '$taxonomySlug'"
    );

    return $alias;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $type = $filterData->getStringParam('attribute_type');

    if ($type !== self::TYPE_TAXONOMY) {
      return [];
    }

    $slug = $filterData->getStringParam('attribute_taxonomy_slug');

    $lookupData = [
      'attribute' => $slug,
    ];

    $termIds = $filterData->getArrayParam('attribute_term_ids');
    $terms = $this->wp->getTerms([
      'taxonomy' => $slug,
      'include' => $termIds,
      'hide_empty' => false,
    ]);

    $lookupData['terms'] = array_map(function($term) {
      return $term->name;
    }, $terms);

    return $lookupData;
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
    $this->validateAttributeData($data);
  }

  public function validateAttributeData(array $data): void {
    $type = $data['attribute_type'];

    if (!in_array($type, [self::TYPE_LOCAL, self::TYPE_TAXONOMY], true)) {
      throw new InvalidFilterException('Invalid attribute type', InvalidFilterException::INVALID_TYPE);
    }

    if ($type === self::TYPE_LOCAL) {
      $name = $data['attribute_local_name'] ?? null;
      if (!is_string($name) || strlen($name) === 0) {
        throw new InvalidFilterException('Missing attribute', InvalidFilterException::MISSING_VALUE);
      }
      $values = $data['attribute_local_values'] ?? [];
      if (!is_array($values) || count($values) === 0) {
        throw new InvalidFilterException('Missing attribute values', InvalidFilterException::MISSING_VALUE);
      }
    }

    if ($type === self::TYPE_TAXONOMY) {
      $attribute_taxonomy_slug = $data['attribute_taxonomy_slug'] ?? null;
      if (!is_string($attribute_taxonomy_slug) || strlen($attribute_taxonomy_slug) === 0) {
        throw new InvalidFilterException('Missing attribute', InvalidFilterException::MISSING_VALUE);
      }
      if (!isset($data['attribute_term_ids']) || !is_array($data['attribute_term_ids']) || count($data['attribute_term_ids']) === 0) {
        throw new InvalidFilterException('Missing attribute terms', InvalidFilterException::MISSING_VALUE);
      }
    }
  }

  private function applyForTaxonomyAttribute(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData) {
    $operator = $filterData->getOperator();

    if ($operator === DynamicSegmentFilterData::OPERATOR_ANY) {
      $this->applyForTaxonomyAnyOperator($queryBuilder, $filterData);
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $this->applyForTaxonomyAnyOperator($queryBuilder, $filterData);
      $countParam = $this->filterHelper->getUniqueParameterName('count');
      $queryBuilder
        ->groupBy('inner_subscriber_id')
        ->having("COUNT(DISTINCT attribute.term_id) = :$countParam")
        ->setParameter($countParam, count($filterData->getArrayParam('attribute_term_ids')));
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $subQuery = $this->filterHelper->getNewSubscribersQueryBuilder();
      $this->applyForTaxonomyAnyOperator($subQuery, $filterData);
      $subscribersTable = $this->filterHelper->getSubscribersTable();
      $queryBuilder->where("{$subscribersTable}.id NOT IN ({$this->filterHelper->getInterpolatedSQL($subQuery)})");
    }
  }

  private function applyForLocalAttribute(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData): void {
    $operator = $filterData->getOperator();
    if ($operator === DynamicSegmentFilterData::OPERATOR_ANY) {
      $this->applyForLocalAnyAttribute($queryBuilder, $filterData);
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $this->applyForLocalAnyAttribute($queryBuilder, $filterData);
      $countParam = $this->filterHelper->getUniqueParameterName('count');
      $queryBuilder
        ->groupBy('inner_subscriber_id')
        ->having("COUNT(DISTINCT postmeta.meta_value) = :$countParam")
        ->setParameter($countParam, count($filterData->getArrayParam('attribute_local_values')));
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $subQuery = $this->filterHelper->getNewSubscribersQueryBuilder();
      $this->applyForLocalAnyAttribute($subQuery, $filterData);
      $subscribersTable = $this->filterHelper->getSubscribersTable();
      $queryBuilder->where("{$subscribersTable}.id NOT IN ({$this->filterHelper->getInterpolatedSQL($subQuery)})");
    }
  }

  private function applyForLocalAnyAttribute(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData): void {
    $attributeName = $filterData->getStringParam('attribute_local_name');
    $attributeValues = $filterData->getArrayParam('attribute_local_values');
    $valuesParam = $this->filterHelper->getUniqueParameterName('attribute_values');
    $keyParam = $this->filterHelper->getUniqueParameterName('attribute_name');
    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder);
    $productAlias = $this->applyProductJoin($queryBuilder, $orderStatsAlias);

    $queryBuilder->innerJoin(
      $productAlias,
      $this->filterHelper->getPrefixedTable('postmeta'),
      'postmeta',
      "$productAlias.product_id = postmeta.post_id AND postmeta.meta_key = :$keyParam AND postmeta.meta_value IN (:$valuesParam)"
    );

    $queryBuilder->setParameter($keyParam, sprintf("attribute_%s", $attributeName));
    $queryBuilder->setParameter($valuesParam, $attributeValues, Connection::PARAM_STR_ARRAY);
  }
}
