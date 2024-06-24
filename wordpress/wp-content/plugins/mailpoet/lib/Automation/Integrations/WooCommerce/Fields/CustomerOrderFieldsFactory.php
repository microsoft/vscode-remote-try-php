<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Fields;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use DateTimeZone;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\CustomerPayload;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use WC_Customer;
use WC_Order;
use WC_Order_Item_Product;
use WC_Product;

class CustomerOrderFieldsFactory {
  /** @var WooCommerce */
  private $wooCommerce;

  /** @var WordPress */
  private $wordPress;

  /** @var TermOptionsBuilder */
  private $termOptionsBuilder;

  /** @var TermParentsLoader */
  private $termParentsLoader;

  public function __construct(
    WordPress $wordPress,
    WooCommerce $wooCommerce,
    TermOptionsBuilder $termOptionsBuilder,
    TermParentsLoader $termParentsLoader
  ) {
    $this->wordPress = $wordPress;
    $this->wooCommerce = $wooCommerce;
    $this->termOptionsBuilder = $termOptionsBuilder;
    $this->termParentsLoader = $termParentsLoader;
  }

  /** @return Field[] */
  public function getFields(): array {
    return [
      new Field(
        'woocommerce:customer:spent-total',
        Field::TYPE_NUMBER,
        __('Total spent', 'mailpoet'),
        function (CustomerPayload $payload, array $params = []) {
          $customer = $payload->getCustomer();
          $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;
          if (!$customer) {
            $order = $payload->getOrder();
            return $order && $this->isInTheLastSeconds($order, $inTheLastSeconds) ? $payload->getTotalSpent() : 0.0;
          }
          return $inTheLastSeconds === null
            ? $payload->getTotalSpent()
            : $this->getRecentSpentTotal($customer, $inTheLastSeconds);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'woocommerce:customer:spent-average',
        Field::TYPE_NUMBER,
        __('Average spent', 'mailpoet'),
        function (CustomerPayload $payload, array $params = []) {
          $customer = $payload->getCustomer();
          $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;

          if (!$customer) {
            $order = $payload->getOrder();
            return $order && $this->isInTheLastSeconds($order, $inTheLastSeconds) ? $payload->getAverageSpent() : 0.0;
          }

          if ($inTheLastSeconds === null) {
            return $payload->getAverageSpent();
          } else {
            $totalSpent = $this->getRecentSpentTotal($customer, $inTheLastSeconds);
            $orderCount = $this->getRecentOrderCount($customer, $inTheLastSeconds);
            return $orderCount > 0 ? ($totalSpent / $orderCount) : 0.0;
          }
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'woocommerce:customer:order-count',
        Field::TYPE_INTEGER,
        __('Order count', 'mailpoet'),
        function (CustomerPayload $payload, array $params = []) {
          $customer = $payload->getCustomer();
          $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;
          if (!$customer) {
            $order = $payload->getOrder();
            return $order && $this->isInTheLastSeconds($order, $inTheLastSeconds) ? $payload->getOrderCount() : 0;
          }
          return $inTheLastSeconds === null
            ? $payload->getOrderCount()
            : $this->getRecentOrderCount($customer, $inTheLastSeconds);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'woocommerce:customer:first-paid-order-date',
        Field::TYPE_DATETIME,
        __('First paid order date', 'mailpoet'),
        function (CustomerPayload $payload) {
          $customer = $payload->getCustomer();
          if (!$customer) {
            $order = $payload->getOrder();
            return $order && $order->is_paid() ? $order->get_date_created() : null;
          }
          return $this->getPaidOrderDate($customer, true);
        }
      ),
      new Field(
        'woocommerce:customer:last-paid-order-date',
        Field::TYPE_DATETIME,
        __('Last paid order date', 'mailpoet'),
        function (CustomerPayload $payload) {
          $customer = $payload->getCustomer();
          if (!$customer) {
            $order = $payload->getOrder();
            return $order && $order->is_paid() ? $order->get_date_created() : null;
          }
          return $this->getPaidOrderDate($customer, false);
        }
      ),
      new Field(
        'woocommerce:customer:purchased-categories',
        Field::TYPE_ENUM_ARRAY,
        __('Purchased categories', 'mailpoet'),
        function (CustomerPayload $payload, array $params = []) {
          $customer = $payload->getCustomer();
          $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;
          if (!$customer) {
            $order = $payload->getOrder();
            $items = $order && $order->is_paid() && $this->isInTheLastSeconds($order, $inTheLastSeconds) ? $order->get_items() : [];
            $ids = [];
            foreach ($items as $item) {
              $product = $item instanceof WC_Order_Item_Product ? $item->get_product() : null;
              $ids = array_merge($ids, $product instanceof WC_Product ? $product->get_category_ids() : []);
            }
            $ids = array_unique($ids);
          } else {
            $ids = $this->getOrderProductTermIds($customer, 'product_cat', $inTheLastSeconds);
          }
          $ids = array_merge($ids, $this->termParentsLoader->getParentIds($ids));
          sort($ids);
          return $ids;
        },
        [
          'options' => $this->termOptionsBuilder->getTermOptions('product_cat'),
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'woocommerce:customer:purchased-tags',
        Field::TYPE_ENUM_ARRAY,
        __('Purchased tags', 'mailpoet'),
        function (CustomerPayload $payload, array $params = []) {
          $customer = $payload->getCustomer();
          $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;
          if (!$customer) {
            $order = $payload->getOrder();
            $items = $order && $order->is_paid() && $this->isInTheLastSeconds($order, $inTheLastSeconds) ? $order->get_items() : [];
            $ids = [];
            foreach ($items as $item) {
              $product = $item instanceof WC_Order_Item_Product ? $item->get_product() : null;
              $ids = array_merge($ids, $product instanceof WC_Product ? $product->get_tag_ids() : []);
            }
            $ids = array_unique($ids);
          } else {
            $ids = $this->getOrderProductTermIds($customer, 'product_tag', $inTheLastSeconds);
          }
          sort($ids);
          return $ids;
        },
        [
          'options' => $this->termOptionsBuilder->getTermOptions('product_tag'),
          'params' => ['in_the_last'],
        ]
      ),
    ];
  }

  private function getRecentSpentTotal(WC_Customer $customer, int $inTheLastSeconds): float {
    $wpdb = $this->wordPress->getWpdb();
    $statuses = array_map(function (string $status) {
      return "wc-$status";
    }, $this->wooCommerce->wcGetIsPaidStatuses());
    $statusesPlaceholder = implode(',', array_fill(0, count($statuses), '%s'));

    if ($this->wooCommerce->isWooCommerceCustomOrdersTableEnabled()) {
      /** @var literal-string $query */
      $query = "
        SELECT SUM(o.total_amount)
        FROM {$wpdb->prefix}wc_orders o
        WHERE o.customer_id = %d
        AND o.status IN ($statusesPlaceholder)
        AND o.date_created_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)
      ";
      $statement = (string)$wpdb->prepare($query, array_merge([$customer->get_id()], $statuses, [$inTheLastSeconds]));
    } else {
      /** @var literal-string $query */
      $query = "
        SELECT SUM(pm_total.meta_value)
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id AND pm_user.meta_key = '_customer_user'
        LEFT JOIN {$wpdb->postmeta} pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total'
        WHERE p.post_type = 'shop_order'
        AND p.post_status IN ($statusesPlaceholder)
        AND pm_user.meta_value = %d
        AND p.post_date_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)
      ";
      $statement = (string)$wpdb->prepare($query, array_merge($statuses, [$customer->get_id(), $inTheLastSeconds]));
    }
    return (float)$wpdb->get_var($statement);
  }

  private function getRecentOrderCount(WC_Customer $customer, int $inTheLastSeconds): int {
    $wpdb = $this->wordPress->getWpdb();
    $statuses = array_keys($this->wooCommerce->wcGetOrderStatuses());
    $statusesPlaceholder = implode(',', array_fill(0, count($statuses), '%s'));

    if ($this->wooCommerce->isWooCommerceCustomOrdersTableEnabled()) {
      /** @var literal-string $query */
      $query = "
        SELECT COUNT(o.id)
        FROM {$wpdb->prefix}wc_orders o
        WHERE o.customer_id = %d
        AND o.status IN ($statusesPlaceholder)
        AND o.date_created_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)
       ";
      $statement = (string)$wpdb->prepare($query, array_merge([$customer->get_id()], $statuses, [$inTheLastSeconds]));
    } else {
      /** @var literal-string $query */
      $query = "
        SELECT COUNT(p.ID)
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id AND pm_user.meta_key = '_customer_user'
        WHERE p.post_type = 'shop_order'
        AND p.post_status IN ($statusesPlaceholder)
        AND pm_user.meta_value = %d
        AND p.post_date_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)
      ";
      $statement = (string)$wpdb->prepare($query, array_merge($statuses, [$customer->get_id(), $inTheLastSeconds]));
    }
    return (int)$wpdb->get_var($statement);
  }

  private function getPaidOrderDate(WC_Customer $customer, bool $fetchFirst): ?DateTimeImmutable {
    $wpdb = $this->wordPress->getWpdb();
    $sorting = $fetchFirst ? 'ASC' : 'DESC';
    $statuses = array_map(function (string $status) {
      return "wc-$status";
    }, $this->wooCommerce->wcGetIsPaidStatuses());
    $statusesPlaceholder = implode(',', array_fill(0, count($statuses), '%s'));

    if ($this->wooCommerce->isWooCommerceCustomOrdersTableEnabled()) {
      /** @var literal-string $query */
      $query = "
        SELECT o.date_created_gmt
        FROM {$wpdb->prefix}wc_orders o
        WHERE o.customer_id = %d
        AND o.status IN ($statusesPlaceholder)
        AND o.total_amount > 0
        ORDER BY o.date_created_gmt {$sorting}
        LIMIT 1
      ";
      $statement = (string)$wpdb->prepare($query, array_merge([$customer->get_id()], $statuses));
    } else {
      /** @var literal-string $query */
      $query = "
        SELECT p.post_date_gmt
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total'
        LEFT JOIN {$wpdb->prefix}postmeta pm_user ON p.ID = pm_user.post_id AND pm_user.meta_key = '_customer_user'
        WHERE p.post_type = 'shop_order'
        AND p.post_status IN ($statusesPlaceholder)
        AND pm_user.meta_value = %d
        AND pm_total.meta_value > 0
        ORDER BY p.post_date_gmt {$sorting}
        LIMIT 1
      ";
      $statement = (string)$wpdb->prepare($query, array_merge($statuses, [$customer->get_id()]));
    }

    $date = $wpdb->get_var($statement);
    return $date ? new DateTimeImmutable($date, new DateTimeZone('GMT')) : null;
  }

  private function getOrderProductTermIds(WC_Customer $customer, string $taxonomy, int $inTheLastSeconds = null): array {
    $wpdb = $this->wordPress->getWpdb();

    $statuses = array_map(function (string $status) {
      return "wc-$status";
    }, $this->wooCommerce->wcGetIsPaidStatuses());
    $statusesPlaceholder = implode(',', array_fill(0, count($statuses), '%s'));

    // get all product categories that the customer has purchased
    if ($this->wooCommerce->isWooCommerceCustomOrdersTableEnabled()) {
      $inTheLastFilter = isset($inTheLastSeconds) ? 'AND o.date_created_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)' : '';
      $orderIdsSubquery = "
        SELECT o.id
        FROM {$wpdb->prefix}wc_orders o
        WHERE o.status IN ($statusesPlaceholder)
        AND o.customer_id = %d
        $inTheLastFilter
      ";
    } else {
      $inTheLastFilter = isset($inTheLastSeconds) ? 'AND p.post_date_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)' : '';
      $orderIdsSubquery = "
        SELECT p.ID
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}postmeta pm_user ON p.ID = pm_user.post_id AND pm_user.meta_key = '_customer_user'
        WHERE p.post_type = 'shop_order'
        AND p.post_status IN ($statusesPlaceholder)
        AND pm_user.meta_value = %d
        $inTheLastFilter
      ";
    }

    /** @var literal-string $query */
    $query = "
      SELECT DISTINCT tt.term_id
      FROM {$wpdb->prefix}term_taxonomy tt
      JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON oi.order_id IN ($orderIdsSubquery) AND oi.order_item_type = 'line_item'
      JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pid ON oi.order_item_id = pid.order_item_id AND pid.meta_key = '_product_id'
      JOIN {$wpdb->prefix}posts p ON pid.meta_value = p.ID
      JOIN {$wpdb->prefix}term_relationships tr ON IF(p.post_type = 'product_variation', p.post_parent, p.ID) = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id
      WHERE tt.taxonomy = %s
      ORDER BY tt.term_id ASC
    ";
    $statement = (string)$wpdb->prepare(
      $query,
      array_merge(
        $statuses,
        [$customer->get_id()],
        isset($inTheLastSeconds) ? [intval($inTheLastSeconds)] : [],
        [(string)($taxonomy)]
      )
    );

    return array_map('intval', $wpdb->get_col($statement));
  }

  private function isInTheLastSeconds(WC_Order $order, ?int $inTheLastSeconds): bool {
    if ($inTheLastSeconds === null) {
      return true;
    }
    return $order->get_date_created() >= new DateTimeImmutable("-$inTheLastSeconds seconds");
  }
}
