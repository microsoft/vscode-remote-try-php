<?php declare(strict_types = 1);

namespace MailPoet\WPCOM;

if (!defined('ABSPATH')) exit;


/**
 * Plan detection documentation:
 * https://github.com/Automattic/wc-calypso-bridge#active-plan-detection
 */
class DotcomHelperFunctions {
  /**
   * Returns true if in the context of WordPress.com Atomic platform.
   *
   * @return bool
   */
  public function isAtomicPlatform(): bool {
    // ATOMIC_CLIENT_ID === '2' corresponds to WordPress.com client on the Atomic platform
    return defined('IS_ATOMIC') && IS_ATOMIC && defined('ATOMIC_CLIENT_ID') && (ATOMIC_CLIENT_ID === '2');
  }

  /**
   * Returns true if the site is on WordPress.com.
   */
  public function isDotcom(): bool {
    return $this->isAtomicPlatform();
  }

  public function isWooExpressPerformance(): bool {
    return function_exists('wc_calypso_bridge_is_woo_express_performance_plan') && wc_calypso_bridge_is_woo_express_performance_plan();
  }

  public function isWooExpressEssential(): bool {
    return function_exists('wc_calypso_bridge_is_woo_express_essential_plan') && wc_calypso_bridge_is_woo_express_essential_plan();
  }

  public function isBusiness(): bool {
    return function_exists('wc_calypso_bridge_is_business_plan') && wc_calypso_bridge_is_business_plan();
  }

  public function isEcommerceTrial(): bool {
    return function_exists('wc_calypso_bridge_is_ecommerce_trial_plan') && wc_calypso_bridge_is_ecommerce_trial_plan();
  }

  public function isEcommerceWPCom(): bool {
    return function_exists('wc_calypso_bridge_is_wpcom_ecommerce_plan') && wc_calypso_bridge_is_wpcom_ecommerce_plan();
  }

  public function isEcommerce(): bool {
    return function_exists('wc_calypso_bridge_is_ecommerce_plan') && wc_calypso_bridge_is_ecommerce_plan();
  }

  /**
   * Returns the plan name for the current site if hosted on WordPress.com.
   * Empty otherwise.
   */
  public function getDotcomPlan(): string {
    if ($this->isWooExpressPerformance()) {
      return 'performance';
    } elseif ($this->isWooExpressEssential()) {
      return 'essential';
    } elseif ($this->isBusiness()) {
      return 'business';
    } elseif ($this->isEcommerceTrial()) {
      return 'ecommerce_trial';
    } elseif ($this->isEcommerceWPCom()) {
      return 'ecommerce_wpcom';
    } elseif ($this->isEcommerce()) {
      return 'ecommerce';
    } else {
      return '';
    }
  }
}
