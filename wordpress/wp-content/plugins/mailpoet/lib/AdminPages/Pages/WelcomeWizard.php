<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\PageRenderer;
use MailPoet\Config\Menu;
use MailPoet\Config\ServicesChecker;
use MailPoet\Settings\SettingsController;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;

class WelcomeWizard {
  const TRACK_LOADDED_VIA_WOOCOMMERCE_SETTING_NAME = 'send_event_that_wizard_was_loaded_via_woocommerce';
  const TRACK_LOADDED_VIA_WOOCOMMERCE_MARKETING_DASHBOARD_SETTING_NAME = 'wizard_loaded_via_woocommerce_marketing_dashboard';

  /** @var PageRenderer */
  private $pageRenderer;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var WooCommerceHelper */
  private $wooCommerceHelper;

  /** @var ServicesChecker */
  private $servicesChecker;

  public function __construct(
    PageRenderer $pageRenderer,
    SettingsController $settings,
    WooCommerceHelper $wooCommerceHelper,
    WPFunctions $wp,
    ServicesChecker $servicesChecker
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->settings = $settings;
    $this->wooCommerceHelper = $wooCommerceHelper;
    $this->wp = $wp;
    $this->servicesChecker = $servicesChecker;
  }

  public function render() {
    if ((bool)(defined('DOING_AJAX') && DOING_AJAX)) return;

    $loadedViaWooCommerce = $this->settings->get(WelcomeWizard::TRACK_LOADDED_VIA_WOOCOMMERCE_SETTING_NAME, false);

    if (!$loadedViaWooCommerce && isset($_GET['mailpoet_wizard_loaded_via_woocommerce'])) {
      // This setting is used to send an event to Mixpanel in another request as, before completing the wizard, Mixpanel is not enabled.
      $this->settings->set(WelcomeWizard::TRACK_LOADDED_VIA_WOOCOMMERCE_SETTING_NAME, 1);
    }

    $loadedViaWooCommerceMarketingDashboard = $this->settings->get(WelcomeWizard::TRACK_LOADDED_VIA_WOOCOMMERCE_MARKETING_DASHBOARD_SETTING_NAME, false);

    if (!$loadedViaWooCommerceMarketingDashboard && isset($_GET['mailpoet_wizard_loaded_via_woocommerce_marketing_dashboard'])) {
      // This setting is used to send an event to Mixpanel in another request as, before completing the wizard, Mixpanel is not enabled.
      $this->settings->set(WelcomeWizard::TRACK_LOADDED_VIA_WOOCOMMERCE_MARKETING_DASHBOARD_SETTING_NAME, 1);
    }


    $premiumKeyValid = $this->servicesChecker->isPremiumKeyValid(false);
    // force MSS key check even if the method isn't active
    $mpApiKeyValid = $this->servicesChecker->isMailPoetAPIKeyValid(false, true);

    $data = [
      'finish_wizard_url' => $this->wp->adminUrl('admin.php?page=' . Menu::MAIN_PAGE_SLUG),
      'admin_email' => $this->wp->getOption('admin_email'),
      'current_wp_user' => $this->wp->wpGetCurrentUser()->to_array(),
      'show_customers_import' => $this->wooCommerceHelper->getCustomersCount() > 0,
      'settings' => $this->getSettings(),
      'premium_key_valid' => !empty($premiumKeyValid),
      'mss_key_valid' => !empty($mpApiKeyValid),
      'has_tracking_settings' => $this->settings->hasSavedValue('analytics') && $this->settings->hasSavedValue('3rd_party_libs'),
      'welcome_wizard_current_step' => $this->settings->get('welcome_wizard_current_step', ''),
    ];
    $this->pageRenderer->displayPage('welcome_wizard.html', $data);
  }

  private function getSettings(): array {
    $settings = $this->settings->getAll();

    $user = $this->wp->wpGetCurrentUser();
    $settings['sender'] = [
      'name' => $user->display_name, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      'address' => $user->user_email, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    ];
    return $settings;
  }
}
