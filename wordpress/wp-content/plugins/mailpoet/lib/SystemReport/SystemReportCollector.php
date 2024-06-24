<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\SystemReport;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronHelper;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Router\Endpoints\CronDaemon;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;

class SystemReportCollector {
  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var WooCommerceHelper */
  private $wooCommerceHelper;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp,
    SubscribersFeature $subscribersFeature,
    WooCommerceHelper $wooCommerceHelper
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
    $this->subscribersFeature = $subscribersFeature;
    $this->wooCommerceHelper = $wooCommerceHelper;
  }

  public function getData($maskApiKey = false) {
    return array_merge($this->getUserData(), $this->getSiteData($maskApiKey));
  }

  public function getUserData() {
    $currentUser = WPFunctions::get()->wpGetCurrentUser();
    $sender = $this->settings->get('sender', ['address' => null]);

    return [
      'name' => $currentUser->display_name, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      'email' => $sender['address'],
    ];
  }

  public function getSiteData($maskApiKey = false) {
    global $wpdb;

    $dbVersion = $wpdb->get_var('SELECT @@VERSION');
    $mta = $this->settings->get('mta');
    $currentTheme = WPFunctions::get()->wpGetTheme();
    $premiumKey = $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME) ?: $this->settings->get(Bridge::API_KEY_SETTING_NAME);

    if ($maskApiKey) {
      $premiumKey = $this->maskApiKey($premiumKey);
    }

    $cronHelper = ContainerWrapper::getInstance()->get(CronHelper::class);
    try {
      $cronPingUrl = $cronHelper->getCronUrl(
        CronDaemon::ACTION_PING
      );
    } catch (\Exception $e) {
      $cronPingUrl = __('Can‘t generate cron URL.', 'mailpoet') . ' (' . $e->getMessage() . ')';
    }

    // the HelpScout Beacon API has a limit of 20 attribute-value pairs (https://developer.helpscout.com/beacon-2/web/javascript-api/#beacon-session-data)
    return [
      'PHP version' => PHP_VERSION,
      'MailPoet Free version' => MAILPOET_VERSION,
      'MailPoet Premium version' => (defined('MAILPOET_PREMIUM_VERSION')) ? MAILPOET_PREMIUM_VERSION : 'N/A',
      'MailPoet Premium/MSS key' => $premiumKey,
      'WordPress version' => $this->wp->getBloginfo('version'),
      'Database version' => $dbVersion,
      'Web server' => (!empty($_SERVER["SERVER_SOFTWARE"])) ? sanitize_text_field(wp_unslash($_SERVER["SERVER_SOFTWARE"])) : 'N/A',
      'Server OS' => (function_exists('php_uname')) ? php_uname() : 'N/A',
      'WP info' => 'WP_MEMORY_LIMIT: ' . WP_MEMORY_LIMIT . ' - WP_MAX_MEMORY_LIMIT: ' . WP_MAX_MEMORY_LIMIT . ' - WP_DEBUG: ' . WP_DEBUG .
        ' - WordPress language: ' . $this->wp->getLocale(),
      'PHP info' => 'PHP max_execution_time: ' . ini_get('max_execution_time') . ' - PHP memory_limit: ' . ini_get('memory_limit') .
        ' - PHP upload_max_filesize: ' . ini_get('upload_max_filesize') . ' - PHP post_max_size: ' . ini_get('post_max_size'),
      'Multisite environment?' => (is_multisite() ? 'Yes' : 'No'),
      'Current Theme' => $currentTheme->get('Name') .
        ' (version ' . $currentTheme->get('Version') . ')',
      'Active Plugin names' => join(", ", $this->wp->getOption('active_plugins')),
      'Sending Method' => $mta['method'],
      'Sending Frequency' => sprintf(
        '%d emails every %d minutes',
        $mta['frequency']['emails'],
        $mta['frequency']['interval']
      ),
      'MailPoet sending info' => "Send all site's emails with: " . ($this->settings->get('send_transactional_emails') ? 'current sending method' : 'default WordPress sending method') .
        ' - Task Scheduler method: ' . $this->settings->get('cron_trigger.method') . ' - Cron ping URL: ' . $cronPingUrl . ' - Default FROM address: ' . $this->settings->get('sender.address') .
        ' - Default Reply-To address: ' . $this->settings->get('reply_to.address') . ' - Bounce Email Address: ' . $this->settings->get('bounce.address'),
      'Total number of subscribers' => $this->subscribersFeature->getSubscribersCount(),
      'Plugin installed at' => $this->settings->get('installed_at'),
      'Installed via WooCommerce onboarding wizard' => $this->wooCommerceHelper->wasMailPoetInstalledViaWooCommerceOnboardingWizard(),
    ];
  }

  protected function maskApiKey($key) {
    // the length of this particular key is an even number.
    // for odd lengths this method will change the total number of characters (which shouldn't be a problem in this context).
    $halfKeyLength = (int)(strlen($key ?? '') / 2);

    return substr($key ?? '', 0, $halfKeyLength) . str_repeat('*', $halfKeyLength);
  }
}
