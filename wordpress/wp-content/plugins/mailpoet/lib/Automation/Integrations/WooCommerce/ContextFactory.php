<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce;

if (!defined('ABSPATH')) exit;


class ContextFactory {

  /** @var WooCommerce */
  private $woocommerce;

  public function __construct(
    WooCommerce $woocommerce
  ) {
    $this->woocommerce = $woocommerce;
  }

  /** @return mixed[] */
  public function getContextData(): array {

    if (!$this->woocommerce->isWooCommerceActive()) {
      return [];
    }

    $context = [
      'order_statuses' => $this->woocommerce->wcGetOrderStatuses(),
      'review_ratings_enabled' => $this->woocommerce->wcReviewRatingsEnabled(),
    ];
    return $context;
  }
}
