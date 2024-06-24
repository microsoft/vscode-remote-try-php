<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce\WooCommerceSubscriptions;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions;

class Helper {


  private $wp;

  public function __construct(
    Functions $wp
  ) {
    $this->wp = $wp;
  }

  public function isWooCommerceSubscriptionsActive() {
    return $this->wp->isPluginActive('woocommerce-subscriptions/woocommerce-subscriptions.php');
  }

  /**
   * @return array<string, string>
   */
  public function wcsGetSubscriptionStatuses(): array {
    if (!function_exists('wcs_get_subscription_statuses')) {
      return [];
    }
    return wcs_get_subscription_statuses();
  }

  public function wcsGetBillingPeriodStrings(): array {
    if (!function_exists('wcs_get_subscription_period_strings')) {
      return [];
    }
    return wcs_get_subscription_period_strings();
  }

  public function wcsGetSubscriptionTrialPeriodStrings(): array {
    if (!function_exists('wcs_get_subscription_trial_period_strings')) {
      return [];
    }
    return wcs_get_subscription_trial_period_strings();
  }

  /**
   * @param int $id
   * @return false|\WC_Subscription
   */
  public function wcsGetSubscription(int $id) {
    if (!function_exists('wcs_get_subscription')) {
      return false;
    }
    return wcs_get_subscription($id);
  }
}
