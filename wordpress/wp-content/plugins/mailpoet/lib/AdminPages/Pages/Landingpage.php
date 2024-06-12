<?php declare(strict_types = 1);

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\Config\Menu;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class Landingpage {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var WPFunctions */
  private $wp;

  private SettingsController $settingsController;

  public function __construct(
    PageRenderer $pageRenderer,
    WPFunctions $wp,
    SettingsController $settingsController
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->wp = $wp;
    $this->settingsController = $settingsController;
  }

  public function render() {
    $data = [
      'welcome_wizard_url' => $this->wp->adminUrl('admin.php?page=' . Menu::WELCOME_WIZARD_PAGE_SLUG),
      'welcome_wizard_current_step' => $this->settingsController->get('welcome_wizard_current_step', ''),
    ];
    $this->pageRenderer->displayPage('landingpage.html', $data);
  }
}
