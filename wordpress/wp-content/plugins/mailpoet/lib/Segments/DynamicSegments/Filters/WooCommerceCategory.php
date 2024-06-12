<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use WP_Term;

class WooCommerceCategory implements Filter {
  const ACTION_CATEGORY = 'purchasedCategory';

  /** @var EntityManager */
  private $entityManager;

  /** @var WPFunctions */
  private $wp;

  /** @var WooFilterHelper */
  private $wooFilterHelper;

  /** @var FilterHelper */
  private $filterHelper;

  public function __construct(
    EntityManager $entityManager,
    FilterHelper $filterHelper,
    WooFilterHelper $wooFilterHelper,
    WPFunctions $wp
  ) {
    $this->entityManager = $entityManager;
    $this->wp = $wp;
    $this->wooFilterHelper = $wooFilterHelper;
    $this->filterHelper = $filterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();

    $operator = $filterData->getOperator();
    $categoryIds = (array)$filterData->getParam('category_ids');
    $categoryIdswithChildrenIds = $this->getCategoriesWithChildren($categoryIds);

    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $parameterSuffix = $filter->getId() ?: Security::generateRandomString();
    $parameterSuffix = (string)$parameterSuffix;

    if ($operator === DynamicSegmentFilterData::OPERATOR_ANY) {
      $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder);
      $this->applyProductJoin($queryBuilder, $orderStatsAlias);
      $this->applyTermRelationshipsJoin($queryBuilder);
      $this->applyTermTaxonomyJoin($queryBuilder, $parameterSuffix);

    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $subQueryCount = 1;
      foreach ($categoryIds as $categoryId) {
        $uniqueParamaterSuffix = Security::generateRandomString();
        $categoryIdWithChildrenIds = $this->getCategoriesWithChildren([$categoryId]);
        $subQuery = $this->filterHelper->getNewSubscribersQueryBuilder();
        $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($subQuery);
        $this->applyProductJoin($subQuery, $orderStatsAlias);
        $this->applyTermRelationshipsJoin($subQuery);
        $this->applyTermTaxonomyJoin($subQuery, $uniqueParamaterSuffix);
        $subQuery->setParameter("category_$uniqueParamaterSuffix", $categoryIdWithChildrenIds, Connection::PARAM_STR_ARRAY);
        $alias = sprintf("subQuery%s", $subQueryCount);
        $queryBuilder->innerJoin(
          $subscribersTable,
          sprintf("(%s)", $this->filterHelper->getInterpolatedSQL($subQuery)),
          $alias,
          "$subscribersTable.id = $alias.id"
        );
        $subQueryCount++;
      }
    } elseif ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      // subQuery with subscriber ids that bought products
      $subQuery = $this->createQueryBuilder($subscribersTable);
      $subQuery->select("DISTINCT $subscribersTable.id");
      $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($subQuery);
      $subQuery = $this->applyProductJoin($subQuery, $orderStatsAlias);
      $subQuery = $this->applyTermRelationshipsJoin($subQuery);
      $subQuery = $this->applyTermTaxonomyJoin($subQuery, $parameterSuffix);
      // apply subQuery for negation
      $queryBuilder->where("$subscribersTable.id NOT IN ({$this->filterHelper->getInterpolatedSQL($subQuery)})");
    }

    return $queryBuilder
      ->setParameter("category_$parameterSuffix", $categoryIdswithChildrenIds, Connection::PARAM_STR_ARRAY);
  }

  private function applyProductJoin(QueryBuilder $queryBuilder, string $orderStatsAlias): QueryBuilder {
    global $wpdb;
    return $queryBuilder->innerJoin(
      $orderStatsAlias,
      $wpdb->prefix . 'wc_order_product_lookup',
      'product',
      "$orderStatsAlias.order_id = product.order_id"
    );
  }

  private function applyTermRelationshipsJoin(QueryBuilder $queryBuilder): QueryBuilder {
    global $wpdb;
    return $queryBuilder->join(
      'product',
      $wpdb->term_relationships, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      'term_relationships',
      'product.product_id = term_relationships.object_id'
    );
  }

  private function applyTermTaxonomyJoin(QueryBuilder $queryBuilder, string $parameterSuffix): QueryBuilder {
    global $wpdb;
    return $queryBuilder->innerJoin(
      'term_relationships',
      $wpdb->term_taxonomy, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      'term_taxonomy',
      "term_taxonomy.term_taxonomy_id=term_relationships.term_taxonomy_id
      AND
      term_taxonomy.term_id IN (:category_$parameterSuffix)"
    );
  }

  private function createQueryBuilder(string $table): QueryBuilder {
    return $this->entityManager->getConnection()
      ->createQueryBuilder()
      ->from($table);
  }

  private function getCategoriesWithChildren(array $categoriesId): array {
    $allIds = [];

    foreach ($categoriesId as $categoryId) {
      $allIds = array_merge($allIds, $this->getAllCategoryIds($categoryId));
    }

    return array_unique($allIds);
  }

  private function getAllCategoryIds(int $categoryId): array {
    $subcategories = $this->wp->getTerms(['taxonomy' => 'product_cat', 'child_of' => $categoryId, 'hide_empty' => false]);
    if (!is_array($subcategories) || empty($subcategories)) {
      return [$categoryId];
    }
    $ids = array_map(function($category) {
      return $category->term_id; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }, $subcategories);
    $ids[] = $categoryId;
    return $ids;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $lookupData = [
      'categories' => [],
    ];
    $categoryIds = $filterData->getArrayParam('category_ids');
    $terms = $this->wp->getTerms('product_cat', ['include' => $categoryIds, 'hide_empty' => false]);
    /** @var WP_Term[] $terms */
    foreach ($terms as $term) {
      $lookupData['categories'][$term->term_id] = $term->name; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }

    return $lookupData;
  }
}
