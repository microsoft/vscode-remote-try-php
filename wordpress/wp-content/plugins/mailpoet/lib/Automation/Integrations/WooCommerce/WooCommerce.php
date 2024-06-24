<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce;

if (!defined('ABSPATH')) exit;


use Automattic\WooCommerce\Utilities\OrderUtil;
use stdClass;
use WC_Order;

class WooCommerce {
  public function isWooCommerceActive(): bool {
    return class_exists('WooCommerce');
  }

  public function wcGetIsPaidStatuses(): array {
    return wc_get_is_paid_statuses();
  }

  /**
   * @return array<string, string>
   */
  public function wcGetOrderStatuses(): array {
    return wc_get_order_statuses();
  }

  public function isWooCommerceCustomOrdersTableEnabled(): bool {
    return $this->isWooCommerceActive()
      && method_exists(OrderUtil::class, 'custom_orders_table_usage_is_enabled')
      && OrderUtil::custom_orders_table_usage_is_enabled();
  }

  /** @return WC_Order[]|stdClass */
  public function wcGetOrders(array $args = []) {
    return wc_get_orders($args);
  }

  /**
   * @param mixed $product
   * @return \WC_Product|null|false
   */
  public function wcGetProduct($product) {
    return wc_get_product($product);
  }

  /**
   * @param int|bool $order
   * @return bool|\WC_Order|\WC_Order_Refund
   */
  public function wcGetOrder($order = false) {
    return wc_get_order($order);
  }

  public function wcGetOrderStatusName(string $status): string {
    return wc_get_order_status_name($status);
  }

  public function wcReviewRatingsEnabled(): bool {
    return wc_review_ratings_enabled();
  }
}
