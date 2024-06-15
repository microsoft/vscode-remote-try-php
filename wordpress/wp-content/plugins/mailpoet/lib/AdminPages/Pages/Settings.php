<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\Config\Installer;
use MailPoet\Config\ServicesChecker;
use MailPoet\Segments\SegmentsSimpleListRepository;
use MailPoet\Services\AuthorizedSenderDomainController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\Hosts;
use MailPoet\Settings\Pages;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscription\Captcha\CaptchaRenderer;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice as WPNotice;

class Settings {
  /** @var PageRenderer */
  private $pageRenderer;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var CaptchaRenderer */
  private $captchaRenderer;

  /** @var SegmentsSimpleListRepository */
  private $segmentsListRepository;

  /** @var Bridge */
  private $bridge;

  /** @var AuthorizedSenderDomainController */
  private $senderDomainController;

  /** @var AssetsController */
  private $assetsController;

  public function __construct(
    AssetsController $assetsController,
    PageRenderer $pageRenderer,
    SettingsController $settings,
    WPFunctions $wp,
    ServicesChecker $servicesChecker,
    CaptchaRenderer $captchaRenderer,
    SegmentsSimpleListRepository $segmentsListRepository,
    Bridge $bridge,
    AuthorizedSenderDomainController $senderDomainController
  ) {
    $this->assetsController = $assetsController;
    $this->pageRenderer = $pageRenderer;
    $this->settings = $settings;
    $this->wp = $wp;
    $this->servicesChecker = $servicesChecker;
    $this->captchaRenderer = $captchaRenderer;
    $this->segmentsListRepository = $segmentsListRepository;
    $this->bridge = $bridge;
    $this->senderDomainController = $senderDomainController;
  }

  public function render() {
    $settings = $this->settings->getAll();

    $premiumKeyValid = $this->servicesChecker->isPremiumKeyValid(false);
    // force MSS key check even if the method isn't active
    $mpApiKeyValid = $this->servicesChecker->isMailPoetAPIKeyValid(false, true);

    $data = [
      'settings' => $settings,
      'segments' => $this->segmentsListRepository->getListWithSubscribedSubscribersCounts(),
      'premium_key_valid' => !empty($premiumKeyValid),
      'mss_key_valid' => !empty($mpApiKeyValid),
      'pages' => Pages::getAll(),
      'current_user' => $this->wp->wpGetCurrentUser(),
      'is_members_plugin_active' => $this->wp->isPluginActive('members/members.php'),
      'hosts' => [
        'web' => Hosts::getWebHosts(),
        'smtp' => Hosts::getSMTPHosts(),
      ],
      'paths' => [
        'root' => ABSPATH,
        'plugin' => dirname(dirname(dirname(__DIR__))),
      ],
      'current_site_title' => $this->wp->getBloginfo('name'),
      'built_in_captcha_supported' => $this->captchaRenderer->isSupported(),
    ];

    $data['authorized_emails'] = [];
    $data['verified_sender_domains'] = [];
    $data['partially_verified_sender_domains'] = [];
    $data['all_sender_domains'] = [];
    $data['sender_restrictions'] = [];

    if ($this->bridge->isMailpoetSendingServiceEnabled()) {
      $data['authorized_emails'] = $this->bridge->getAuthorizedEmailAddresses();
      $data['verified_sender_domains'] = $this->senderDomainController->getFullyVerifiedSenderDomains(true);
      $data['partially_verified_sender_domains'] = $this->senderDomainController->getPartiallyVerifiedSenderDomains(true);
      $data['all_sender_domains'] = $this->senderDomainController->getAllSenderDomains();
      $data['sender_restrictions'] = [
        'lowerLimit' => AuthorizedSenderDomainController::LOWER_LIMIT,
      ];
    }

    $data = array_merge($data, Installer::getPremiumStatus());

    if (isset($_GET['enable-customizer-notice'])) {
      $notice = new WPNotice(WPNotice::TYPE_ERROR, _x(
        'You need to have WooCommerce active to access the MailPoet email customizer for WooCommerce.',
        'Notice in Settings when WooCommerce is not enabled',
        'mailpoet'
      ));
      $notice->displayWPNotice();
    }

    $this->assetsController->setupSettingsDependencies();
    $this->pageRenderer->displayPage('settings.html', $data);
  }
}
