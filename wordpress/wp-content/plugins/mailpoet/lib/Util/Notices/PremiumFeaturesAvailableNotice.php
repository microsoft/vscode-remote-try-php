<?php declare(strict_types = 1);

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Installer;
use MailPoet\Config\ServicesChecker;
use MailPoet\Util\Helpers;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class PremiumFeaturesAvailableNotice {

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var Installer */
  private $premiumInstaller;

  /** @var WPFunctions */
  private $wp;

  const DISMISS_NOTICE_TIMEOUT_SECONDS = 2592000; // 30 days
  const OPTION_NAME = 'dismissed-premium-features-available-notice';

  public function __construct(
    SubscribersFeature $subscribersFeature,
    ServicesChecker $servicesChecker,
    WPFunctions $wp
  ) {
    $this->subscribersFeature = $subscribersFeature;
    $this->servicesChecker = $servicesChecker;
    $this->premiumInstaller = new Installer(Installer::PREMIUM_PLUGIN_PATH);
    $this->wp = $wp;
  }

  public function init($shouldDisplay): ?Notice {
    if (
      $shouldDisplay
      && !$this->wp->getTransient(self::OPTION_NAME)
      && $this->subscribersFeature->hasValidPremiumKey()
      && (!Installer::isPluginInstalled(Installer::PREMIUM_PLUGIN_SLUG) || !$this->servicesChecker->isPremiumPluginActive())
    ) {
      return $this->display();
    }

    return null;
  }

  public function display(): Notice {
    $noticeString = __('Your current MailPoet plan includes advanced features, but they require the MailPoet Premium plugin to be installed and activated.', 'mailpoet');

    // We reuse already existing translations from premium_messages.tsx
    if (!Installer::isPluginInstalled(Installer::PREMIUM_PLUGIN_SLUG)) {
      $noticeString .= ' [link]' . __('Download MailPoet Premium plugin', 'mailpoet') . '[/link]';
      $link = $this->premiumInstaller->generatePluginDownloadUrl();
      $attributes = ['target' => '_blank']; // Only download link should be opened in a new tab
    } else {
      $noticeString .= ' [link]' . __('Activate MailPoet Premium plugin', 'mailpoet') . '[/link]';
      $link = $this->premiumInstaller->generatePluginActivationUrl(Installer::PREMIUM_PLUGIN_PATH);
      $attributes = [];
    }

    $noticeString = Helpers::replaceLinkTags($noticeString, $link, $attributes);
    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    return Notice::displaySuccess($noticeString, $extraClasses, self::OPTION_NAME);
  }

  public function disable(): void {
    WPFunctions::get()->setTransient(self::OPTION_NAME, true, self::DISMISS_NOTICE_TIMEOUT_SECONDS);
  }
}
