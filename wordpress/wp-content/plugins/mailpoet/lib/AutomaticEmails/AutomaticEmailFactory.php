<?php declare(strict_types = 1);

namespace MailPoet\AutomaticEmails;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\WooCommerce;
use MailPoet\DI\ContainerWrapper;

class AutomaticEmailFactory {
  /** @var ContainerWrapper */
  private $container;

  public function __construct(
    ContainerWrapper $container
  ) {
    $this->container = $container;
  }

  public function createWooCommerceEmail(): WooCommerce {
    return $this->container->get(WooCommerce::class);
  }
}
