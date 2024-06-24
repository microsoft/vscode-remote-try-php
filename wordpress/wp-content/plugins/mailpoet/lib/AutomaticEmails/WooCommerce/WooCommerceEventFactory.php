<?php declare(strict_types = 1);

namespace MailPoet\AutomaticEmails\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart;
use MailPoet\AutomaticEmails\WooCommerce\Events\FirstPurchase;
use MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedInCategory;
use MailPoet\AutomaticEmails\WooCommerce\Events\PurchasedProduct;
use MailPoet\DI\ContainerWrapper;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class WooCommerceEventFactory {
  public const EVENTS_MAP = [
    'AbandonedCart' => AbandonedCart::class,
    'FirstPurchase' => FirstPurchase::class,
    'PurchasedInCategory' => PurchasedInCategory::class,
    'PurchasedProduct' => PurchasedProduct::class,
  ];

  /** @var ContainerWrapper */
  private $container;

  public function __construct(
    ContainerWrapper $container
  ) {
    $this->container = $container;
  }

  /** @return object|null */
  public function createEvent(string $eventName) {
    $eventClass = self::EVENTS_MAP[$eventName] ?? null;

    try {
      return $eventClass ? $this->container->get($eventClass) : null;
    } catch (ServiceNotFoundException $e) {
      return null;
    }
  }
}
