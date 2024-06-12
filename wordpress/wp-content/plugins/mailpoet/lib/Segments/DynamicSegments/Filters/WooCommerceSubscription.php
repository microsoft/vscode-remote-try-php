<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Util\DBCollationChecker;
use MailPoet\Util\Security;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class WooCommerceSubscription implements Filter {
  const ACTION_HAS_ACTIVE = 'hasActiveSubscription';

  /** @var EntityManager */
  private $entityManager;

  /** @var WooCommerceHelper */
  private $woocommerceHelper;

  /** @var DBCollationChecker */
  private $collationChecker;

  public function __construct(
    EntityManager $entityManager,
    DBCollationChecker $collationChecker,
    WooCommerceHelper $woocommerceHelper
  ) {
    $this->entityManager = $entityManager;
    $this->collationChecker = $collationChecker;
    $this->woocommerceHelper = $woocommerceHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $productIds = $filterData->getParam('product_ids');
    $operator = $filterData->getParam('operator');
    $parameterSuffix = $filter->getId() ?: Security::generateRandomString();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    // ALL OF
    if ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $this->applyPostmetaAndPostJoin($queryBuilder);
      $this->applyOrderItemsJoin($queryBuilder);
      $this->applyOrderItemmetaJoin($queryBuilder);
      return $queryBuilder
        ->andWhere("itemmeta.meta_value IN (:products" . $parameterSuffix . ")")
        ->groupBy("$subscribersTable.id")
        ->having("COUNT($subscribersTable.id) = :count$parameterSuffix")
        ->setParameter('products' . $parameterSuffix, $productIds, Connection::PARAM_STR_ARRAY)
        ->setParameter('count' . $parameterSuffix, count($productIds));
    }

    // NONE OF
    if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $subQueryBuilder = $this->entityManager->getConnection()
        ->createQueryBuilder()
        ->from($subscribersTable)
        ->select("DISTINCT $subscribersTable.id");
      $this->applyPostmetaAndPostJoin($subQueryBuilder);
      $this->applyOrderItemsJoin($subQueryBuilder);
      $this->applyOrderItemmetaJoin($subQueryBuilder);
      $subQueryBuilder
        ->andWhere("itemmeta.meta_value IN (:products" . $parameterSuffix . ")");
      return $queryBuilder->where("{$subscribersTable}.id NOT IN ({$subQueryBuilder->getSQL()})")
        ->setParameter('products' . $parameterSuffix, $productIds, Connection::PARAM_STR_ARRAY);
    }

    // ANY
    $this->applyPostmetaAndPostJoin($queryBuilder);
    $this->applyOrderItemsJoin($queryBuilder);
    $this->applyOrderItemmetaJoin($queryBuilder);
    return $queryBuilder
      ->andWhere("itemmeta.meta_value IN (:products" . $parameterSuffix . ")")
      ->setParameter('products' . $parameterSuffix, $productIds, Connection::PARAM_STR_ARRAY);
  }

  private function applyPostmetaAndPostJoin(QueryBuilder $queryBuilder): QueryBuilder {
    global $wpdb;
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    if ($this->woocommerceHelper->isWooCommerceCustomOrdersTableEnabled()) {
      $collation = $this->collationChecker->getCollateIfNeeded(
        $subscribersTable,
        'email',
        $wpdb->prefix . 'wc_orders',
        'billing_email'
      );

      return $queryBuilder->innerJoin(
        $subscribersTable,
        $wpdb->prefix . 'wc_orders',
        'wc_orders',
        "{$subscribersTable}.email = wc_orders.billing_email $collation AND wc_orders.status IN(\"wc-active\", \"wc-pending-cancel\")"
      );
    }

    return $queryBuilder->innerJoin(
      $subscribersTable,
      $wpdb->postmeta,
      'postmeta',
      "postmeta.meta_key = '_customer_user' AND $subscribersTable.wp_user_id=postmeta.meta_value"
    )->innerJoin(
      'postmeta',
      $wpdb->posts,
      'posts',
      'postmeta.post_id = posts.id AND posts.post_type = "shop_subscription" AND posts.post_status IN("wc-active", "wc-pending-cancel")'
    );
  }

  private function applyOrderItemsJoin(QueryBuilder $queryBuilder): QueryBuilder {
    global $wpdb;
    if ($this->woocommerceHelper->isWooCommerceCustomOrdersTableEnabled()) {
      return $queryBuilder->innerJoin(
        'wc_orders',
        $wpdb->prefix . 'woocommerce_order_items',
        'items',
        'wc_orders.id = items.order_id AND order_item_type = "line_item"'
      );
    }

    return $queryBuilder->innerJoin(
      'postmeta',
      $wpdb->prefix . 'woocommerce_order_items',
      'items',
      'postmeta.post_id = items.order_id AND order_item_type = "line_item"'
    );
  }

  private function applyOrderItemmetaJoin(QueryBuilder $queryBuilder): QueryBuilder {
    global $wpdb;
    return $queryBuilder->innerJoin(
      'items',
      $wpdb->prefix . 'woocommerce_order_itemmeta',
      'itemmeta',
      "itemmeta.order_item_id=items.order_item_id AND itemmeta.meta_key='_product_id'"
    );
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
