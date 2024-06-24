<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\Util\License\Features\CapabilitiesManager;
use MailPoet\WP\Functions as WPFunctions;

class Upgrade {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var WPFunctions */
  private $wp;

  private CapabilitiesManager $capabilitiesManager;

  public function __construct(
    PageRenderer $pageRenderer,
    WPFunctions $wp,
    CapabilitiesManager $capabilitiesManager
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->wp = $wp;
    $this->capabilitiesManager = $capabilitiesManager;
  }

  public function render() {
    $data = [
      'current_wp_user' => $this->wp->wpGetCurrentUser()->to_array(),
    ];

    // @todo change this after a/b test to keep only one of the pages
    if ($this->capabilitiesManager->showNewUpgradePage()) {
      $data = [
        'current_mailpoet_plan_tier' => $this->capabilitiesManager->getTier(),
      ];

      $this->pageRenderer->displayPage('upgrade_tiers.html', $data);
      return;
    }

    $this->pageRenderer->displayPage('upgrade.html', $data);
  }
}
