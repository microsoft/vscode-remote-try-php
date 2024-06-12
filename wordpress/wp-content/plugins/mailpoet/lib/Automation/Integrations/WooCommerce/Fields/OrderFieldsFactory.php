<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Fields;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\OrderPayload;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use WC_Order;
use WC_Order_Item_Product;
use WC_Payment_Gateway;
use WC_Product;
use WP_Post;

class OrderFieldsFactory {
  /** @var TermOptionsBuilder */
  private $termOptionsBuilder;

  /** @var TermParentsLoader */
  private $termParentsLoader;

  /** @var WordPress */
  private $wordPress;

  /** @var WooCommerce */
  private $wooCommerce;

  public function __construct(
    TermOptionsBuilder $termOptionsBuilder,
    TermParentsLoader $termParentsLoader,
    WordPress $wordPress,
    WooCommerce $wooCommerce
  ) {
    $this->termOptionsBuilder = $termOptionsBuilder;
    $this->termParentsLoader = $termParentsLoader;
    $this->wordPress = $wordPress;
    $this->wooCommerce = $wooCommerce;
  }

  /** @return Field[] */
  public function getFields(): array {
    return array_merge(
      [
        new Field(
          'woocommerce:order:billing-company',
          Field::TYPE_STRING,
          __('Billing company', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_billing_company();
          }
        ),
        new Field(
          'woocommerce:order:billing-phone',
          Field::TYPE_STRING,
          __('Billing phone', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_billing_phone();
          }
        ),
        new Field(
          'woocommerce:order:billing-city',
          Field::TYPE_STRING,
          __('Billing city', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_billing_city();
          }
        ),
        new Field(
          'woocommerce:order:billing-postcode',
          Field::TYPE_STRING,
          __('Billing postcode', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_billing_postcode();
          }
        ),
        new Field(
          'woocommerce:order:billing-state',
          Field::TYPE_STRING,
          __('Billing state/county', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_billing_state();
          }
        ),
        new Field(
          'woocommerce:order:billing-country',
          Field::TYPE_ENUM,
          __('Billing country', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_billing_country();
          },
          [
            'options' => $this->getBillingCountryOptions(),
          ]
        ),
        new Field(
          'woocommerce:order:shipping-company',
          Field::TYPE_STRING,
          __('Shipping company', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_shipping_company();
          }
        ),
        new Field(
          'woocommerce:order:shipping-phone',
          Field::TYPE_STRING,
          __('Shipping phone', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_shipping_phone();
          }
        ),
        new Field(
          'woocommerce:order:shipping-city',
          Field::TYPE_STRING,
          __('Shipping city', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_shipping_city();
          }
        ),
        new Field(
          'woocommerce:order:shipping-postcode',
          Field::TYPE_STRING,
          __('Shipping postcode', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_shipping_postcode();
          }
        ),
        new Field(
          'woocommerce:order:shipping-state',
          Field::TYPE_STRING,
          __('Shipping state/county', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_shipping_state();
          }
        ),
        new Field(
          'woocommerce:order:shipping-country',
          Field::TYPE_ENUM,
          __('Shipping country', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_shipping_country();
          },
          [
            'options' => $this->getShippingCountryOptions(),
          ]
        ),
        new Field(
          'woocommerce:order:created-date',
          Field::TYPE_DATETIME,
          __('Created date', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_date_created();
          }
        ),
        new Field(
          'woocommerce:order:paid-date',
          Field::TYPE_DATETIME,
          __('Paid date', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_date_paid();
          }
        ),
        new Field(
          'woocommerce:order:customer-note',
          Field::TYPE_STRING,
          __('Customer provided note', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_customer_note();
          }
        ),
        new Field(
          'woocommerce:order:payment-method',
          Field::TYPE_ENUM,
          __('Payment method', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_payment_method();
          },
          [
            'options' => $this->getOrderPaymentOptions(),
          ]
        ),
        new Field(
          'woocommerce:order:status',
          Field::TYPE_ENUM,
          __('Status', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_status();
          },
          [
            'options' => $this->getOrderStatusOptions(),
          ]
        ),
        new Field(
          'woocommerce:order:total',
          Field::TYPE_NUMBER,
          __('Total', 'mailpoet'),
          function (OrderPayload $payload) {
            return (float)$payload->getOrder()->get_total();
          }
        ),
        new Field(
          'woocommerce:order:coupons',
          Field::TYPE_ENUM_ARRAY,
          __('Used coupons', 'mailpoet'),
          function (OrderPayload $payload) {
            return $payload->getOrder()->get_coupon_codes();
          },
          [
            'options' => $this->getCouponOptions(),
          ]
        ),
        new Field(
          'woocommerce:order:is-first-order',
          Field::TYPE_BOOLEAN,
          __('Is first order', 'mailpoet'),
          function (OrderPayload $payload) {
            $order = $payload->getOrder();
            return !$this->previousOrderExists($order);
          }
        ),
        new Field(
          'woocommerce:order:categories',
          Field::TYPE_ENUM_ARRAY,
          __('Categories', 'mailpoet'),
          function (OrderPayload $payload) {
            $products = $this->getProducts($payload->getOrder());
            $categoryIds = [];
            foreach ($products as $product) {
              $categoryIds = array_merge($categoryIds, $product->get_category_ids());
            }
            $categoryIds = array_merge($categoryIds, $this->termParentsLoader->getParentIds($categoryIds));
            sort($categoryIds);
            return array_unique($categoryIds);
          },
          [
            'options' => $this->termOptionsBuilder->getTermOptions('product_cat'),
          ]
        ),
        new Field(
          'woocommerce:order:tags',
          Field::TYPE_ENUM_ARRAY,
          __('Tags', 'mailpoet'),
          function (OrderPayload $payload) {
            $products = $this->getProducts($payload->getOrder());
            $tagIds = [];
            foreach ($products as $product) {
              $tagIds = array_merge($tagIds, $product->get_tag_ids());
            }
            sort($tagIds);
            return array_unique($tagIds);
          },
          [
            'options' => $this->termOptionsBuilder->getTermOptions('product_tag'),
          ]
        ),
        new Field(
          'woocommerce:order:products',
          Field::TYPE_ENUM_ARRAY,
          __('Products', 'mailpoet'),
          function (OrderPayload $payload) {
            $products = $this->getProducts($payload->getOrder());
            return array_map(function (WC_Product $product) {
              return $product->get_id();
            }, $products);
          },
          [
            'options' => $this->getProductOptions(),
          ]
        ),
      ]
    );
  }

  private function getBillingCountryOptions(): array {
    $options = [];
    foreach (WC()->countries->get_allowed_countries() as $code => $name) {
      $options[] = ['id' => $code, 'name' => $name];
    }
    return $options;
  }

  private function getShippingCountryOptions(): array {
    $options = [];
    foreach (WC()->countries->get_shipping_countries() as $code => $name) {
      $options[] = ['id' => $code, 'name' => $name];
    }
    return $options;
  }

  private function getOrderPaymentOptions(): array {
    $gateways = WC()->payment_gateways()->get_available_payment_gateways();
    $options = [];
    foreach ($gateways as $gateway) {
      if ($gateway instanceof WC_Payment_Gateway && $gateway->enabled === 'yes') {
        $options[] = ['id' => $gateway->id, 'name' => $gateway->title];
      }
    }
    return $options;
  }

  private function getOrderStatusOptions(): array {
    $statuses = $this->wooCommerce->wcGetOrderStatuses();
    $options = [];
    foreach ($statuses as $id => $name) {
      $options[] = [
        'id' => substr($id, 0, 3) === 'wc-' ? substr($id, 3) : $id,
        'name' => $name,
      ];
    }
    return $options;
  }

  private function getCouponOptions(): array {
    $coupons = $this->wordPress->getPosts([
      'post_type' => 'shop_coupon',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'name',
      'order' => 'asc',
    ]);

    $options = [];
    foreach ($coupons as $coupon) {
      if ($coupon instanceof WP_Post) {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $options[] = ['id' => $coupon->post_title, 'name' => $coupon->post_title];
      }
    }
    return $options;
  }

  private function previousOrderExists(WC_Order $order): bool {
    $dateCreated = $order->get_date_created() ?? new DateTimeImmutable('now', $this->wordPress->wpTimezone());
    $query = [
      'date_created' => '<=' . $dateCreated->getTimestamp(),
      'limit' => 2,
      'return' => 'ids',
    ];

    if ($order->get_customer_id() > 0) {
      $query['customer_id'] = $order->get_customer_id();
    } else {
      $query['billing_email'] = $order->get_billing_email();
    }

    $orderIds = (array)$this->wooCommerce->wcGetOrders($query);
    return count($orderIds) > 1 && min($orderIds) < $order->get_id();
  }

  /** @return WC_Product[] */
  private function getProducts(WC_Order $order): array {
    $products = [];
    foreach ($order->get_items() as $item) {
      if (!$item instanceof WC_Order_Item_Product) {
        continue;
      }

      $product = $item->get_product();
      if (!$product instanceof WC_Product) {
        continue;
      }
      if (!$product->is_type('variation')) {
        $products[] = $product;
        continue;
      }

      $parentProduct = $this->wooCommerce->wcGetProduct($product->get_parent_id());
      if (!$parentProduct instanceof WC_Product) {
        continue;
      }
      $products[] = $parentProduct;
    }
    return array_unique($products);
  }

  private function getProductOptions(): array {
    $wpdb = $this->wordPress->getWpdb();
    $products = $wpdb->get_results(
      "
        SELECT ID, post_title
        FROM {$wpdb->posts}
        WHERE post_type = 'product'
        AND post_status = 'publish'
        ORDER BY post_title ASC
      ",
      ARRAY_A
    );

    return array_map(function ($product) {
      /** @var array{ID:int, post_title:string} $product */
      $id = $product['ID'];
      $title = $product['post_title'];
      return ['id' => (int)$id, 'name' => "$title (#$id)"];
    }, (array)$products);
  }
}
