<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use Automattic\WooCommerce\Admin\API\Reports\Customers\Stats\Query;
use MailPoet\DI\ContainerWrapper;
use MailPoet\RuntimeException;
use MailPoet\WP\Functions as WPFunctions;

class Helper {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function isWooCommerceActive() {
    return class_exists('WooCommerce') && $this->wp->isPluginActive('woocommerce/woocommerce.php');
  }

  public function getWooCommerceVersion() {
    return $this->isWooCommerceActive() ? get_plugin_data(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')['Version'] : null;
  }

  public function getPurchaseStates(): array {

    return (array)$this->wp->applyFilters(
      'mailpoet_purchase_order_states',
      ['completed']
    );
  }

  public function isWooCommerceBlocksActive($min_version = '') {
    if (!class_exists('\Automattic\WooCommerce\Blocks\Package')) {
      return false;
    }
    if ($min_version) {
      return version_compare(\Automattic\WooCommerce\Blocks\Package::get_version(), $min_version, '>=');
    }
    return true;
  }

  public function isWooCommerceCustomOrdersTableEnabled(): bool {
    if (
      $this->isWooCommerceActive()
      && method_exists('\Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled')
      && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()
    ) {
      return true;
    }

    return false;
  }

  public function WC() {
    return WC();
  }

  public function wcGetCustomerOrderCount($userId) {
    return wc_get_customer_order_count($userId);
  }

  public function wcGetOrder($order = false) {
    return wc_get_order($order);
  }

  public function wcGetOrders(array $args) {
    return wc_get_orders($args);
  }

  public function wcCreateOrder(array $args) {
    return wc_create_order($args);
  }

  public function wcPrice($price, array $args = []) {
    return wc_price($price, $args);
  }

  public function wcGetProduct($theProduct = false) {
    return wc_get_product($theProduct);
  }

  public function wcGetPageId(string $page): ?int {
    if ($this->isWooCommerceActive()) {
      return (int)wc_get_page_id($page);
    }
    return null;
  }

  public function wcGetPriceDecimals(): int {
    return wc_get_price_decimals();
  }

  public function wcGetPriceDecimalSeperator(): string {
    return wc_get_price_decimal_separator();
  }

  public function wcGetPriceThousandSeparator(): string {
    return wc_get_price_thousand_separator();
  }

  public function getWoocommercePriceFormat(): string {
    return get_woocommerce_price_format();
  }

  public function getWoocommerceCurrency() {
    return get_woocommerce_currency();
  }

  public function getWoocommerceCurrencySymbol() {
    return get_woocommerce_currency_symbol();
  }

  public function woocommerceFormField($key, $args, $value) {
    return woocommerce_form_field($key, $args, $value);
  }

  public function wcLightOrDark($color, $dark, $light) {
    return wc_light_or_dark($color, $dark, $light);
  }

  public function wcHexIsLight($color) {
    return wc_hex_is_light($color);
  }

  public function getOrdersCountCreatedBefore(string $dateTime): int {
    $ordersCount = $this->wcGetOrders([
      'status' => 'all',
      'type' => 'shop_order',
      'date_created' => '<' . $dateTime,
      'limit' => 1,
      'paginate' => true,
    ])->total;

    return intval($ordersCount);
  }

  public function getRawPrice($price, array $args = []) {
    $htmlPrice = $this->wcPrice($price, $args);
    return html_entity_decode(strip_tags($htmlPrice));
  }

  public function getAllowedCountries(): array {
    return (new \WC_Countries)->get_allowed_countries() ?? [];
  }

  public function getCustomersCount(): int {
    if (!$this->isWooCommerceActive() || !class_exists(Query::class)) {
      return 0;
    }
    $query = new Query([
      'fields' => ['customers_count'],
    ]);
    // Query::get_data declares it returns array but the underlying DataStore returns stdClass
    $result = (array)$query->get_data();
    return isset($result['customers_count']) ? intval($result['customers_count']) : 0;
  }

  public function wasMailPoetInstalledViaWooCommerceOnboardingWizard(): bool {
    $wp = ContainerWrapper::getInstance()->get(WPFunctions::class);
    $installedViaWooCommerce = false;
    $wooCommerceOnboardingProfile = $wp->getOption('woocommerce_onboarding_profile');

    if (
      is_array($wooCommerceOnboardingProfile)
      && isset($wooCommerceOnboardingProfile['business_extensions'])
      && is_array($wooCommerceOnboardingProfile['business_extensions'])
      && in_array('mailpoet', $wooCommerceOnboardingProfile['business_extensions'])
    ) {
      $installedViaWooCommerce = true;
    }

    return $installedViaWooCommerce;
  }

  public function getOrdersTableName() {
    if (!method_exists('\Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore', 'get_orders_table_name')) {
      throw new RuntimeException('Cannot get orders table name when running a WooCommerce version that doesn\'t support custom order tables.');
    }

    return \Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::get_orders_table_name();
  }

  public function getAddressesTableName() {
    if (!method_exists('\Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore', 'get_addresses_table_name')) {
      throw new RuntimeException('Cannot get addresses table name when running a WooCommerce version that doesn\'t support custom order tables.');
    }

    return \Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::get_addresses_table_name();
  }

  public function wcGetCouponTypes(): array {
    return wc_get_coupon_types();
  }

  public function wcGetCouponCodeById(int $id): string {
    return wc_get_coupon_code_by_id($id);
  }

  /**
   * @param mixed $data Coupon data, object, ID or code.
   */
  public function createWcCoupon($data) {
    return new \WC_Coupon($data);
  }

  public function getOrderStatuses(): array {
    return wc_get_order_statuses();
  }

  /**
   * @return array|\WP_Post[]
   */
  public function getCouponList(
    int $pageSize = 10,
    int $pageNumber = 1,
    ?string $discountType = null,
    ?string $search = null,
    array $includeCouponIds = []
  ): array {
    $args = [
      'posts_per_page' => $pageSize,
      'orderby' => 'name',
      'order' => 'asc',
      'post_type' => 'shop_coupon',
      'post_status' => 'publish',
      'paged' => $pageNumber,
    ];
    // Filtering coupons by discount type
    if ($discountType) {
      $args['meta_key'] = 'discount_type';
      $args['meta_value'] = $discountType;
    }
    // Search coupon by a query string
    if ($search) {
      $args['s'] = $search;
    }

    $includeCoupons = [];
    // We need to include the coupon with the given ID in the first page
    if ($includeCouponIds && $pageNumber === 1) {
      $includeArgs = $args;
      $includeArgs['include'] = $includeCouponIds;
      $includeCoupons = $this->wp->getPosts($includeArgs);
    }

    // We remove duplicates because one of the remaining pages might contain the coupon with the given ID
    $result = array_merge($includeCoupons, $this->wp->getPosts($args));
    $result = array_unique($result, SORT_REGULAR);
    return array_values($result);
  }

  public function wcGetPriceDecimalSeparator() {
    return wc_get_price_decimal_separator();
  }

  public function getLatestCoupon(): ?string {
    $coupons = $this->wp->getPosts([
      'numberposts' => 1,
      'orderby' => 'date_created',
      'order' => 'desc',
      'post_type' => 'shop_coupon',
      'post_status' => 'publish',
    ]);
    $coupon = reset($coupons);

    return $coupon ? $coupon->post_title : null; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }

  public function getPaymentGateways() {
    return $this->WC()->payment_gateways();
  }

  /**
   * Returns a list of all available shipping methods formatted
   * in a way to be used in the 'used shipping method' segment.
   */
  public function getShippingMethodInstancesData(): array {
    $shippingZones = \WC_Shipping_Zones::get_zones();
    $formattedShippingMethodData = [];

    foreach ($shippingZones as $shippingZone) {
      $formattedShippingMethodData = array_merge(
        $formattedShippingMethodData,
        $this->formatShippingMethods($shippingZone['shipping_methods'], $shippingZone['zone_name'])
      );
    }

    // special shipping zone that includes locations not covered by the configured shipping zones
    $outOfCoverageShippingZone = new \WC_Shipping_Zone(0);
    $formattedShippingMethodData = array_merge(
      $formattedShippingMethodData,
      $this->formatShippingMethods($outOfCoverageShippingZone->get_shipping_methods(), $outOfCoverageShippingZone->get_zone_name())
    );

    $keyedZones = [];

    foreach ($formattedShippingMethodData as $shippingMethodArray) {
      $keyedZones[$shippingMethodArray['instanceId']] = $shippingMethodArray;
    }

    return $keyedZones;
  }

  public function wcGetAttributeTaxonomies(): array {
    return wc_get_attribute_taxonomies();
  }

  protected function formatShippingMethods(array $shippingMethods, string $shippingZoneName): array {
    $formattedShippingMethods = [];

    foreach ($shippingMethods as $shippingMethod) {
      $formattedShippingMethods[] = [
        'instanceId' => (string)$shippingMethod->instance_id, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'name' => "{$shippingMethod->title} ({$shippingZoneName})",
      ];
    }

    return $formattedShippingMethods;
  }

  /**
   * Check whether the current request is processing a WooCommerce checkout.
   * Works for both the normal checkout and the block checkout.
   *
   * This solution is not ideal, but I checked with a few WooCommerce developers,
   * and it is what they suggested. There is no helper function provided by Woo
   * for this.
   *
   * @return bool
   */
  public function isCheckoutRequest(): bool {
    $requestUri = !empty($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    $isRegularCheckout = is_checkout();
    $isBlockCheckout = WC()->is_rest_api_request()
      && (strpos($requestUri, 'wc/store/checkout') !== false || strpos($requestUri, 'wc/store/v1/checkout') !== false);

    return $isRegularCheckout || $isBlockCheckout;
  }
}
