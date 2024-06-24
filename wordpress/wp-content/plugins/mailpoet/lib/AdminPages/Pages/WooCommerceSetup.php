<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\Config\Menu;
use MailPoet\WooCommerce\Helper;
use MailPoet\WP\Functions as WPFunctions;

class WooCommerceSetup {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var WPFunctions */
  private $wp;

  /** @var Helper */
  private $wooCommerceHelper;

  public function __construct(
    PageRenderer $pageRenderer,
    Helper $wooCommerceHelper,
    WPFunctions $wp
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->wooCommerceHelper = $wooCommerceHelper;
    $this->wp = $wp;
  }

  public function render() {
    if ((bool)(defined('DOING_AJAX') && DOING_AJAX)) return;
    $data = [
      'finish_wizard_url' => $this->wp->adminUrl('admin.php?page=' . Menu::MAIN_PAGE_SLUG),
      'show_customers_import' => $this->wooCommerceHelper->getCustomersCount() > 0,
    ];
    $this->pageRenderer->displayPage('woocommerce_setup.html', $data);
  }
}
