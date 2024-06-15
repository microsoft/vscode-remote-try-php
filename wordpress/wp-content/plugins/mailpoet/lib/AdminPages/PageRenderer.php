<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\Pages\WelcomeWizard;
use MailPoet\Cache\TransientCache;
use MailPoet\Config\Installer;
use MailPoet\Config\Menu;
use MailPoet\Config\Renderer;
use MailPoet\Config\ServicesChecker;
use MailPoet\Cron\Workers\SubscribersCountCacheRecalculation;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\TagEntity;
use MailPoet\Features\FeaturesController;
use MailPoet\Referrals\ReferralDetector;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Settings\UserFlagsController;
use MailPoet\Tags\TagRepository;
use MailPoet\Tracy\DIPanel\DIPanel;
use MailPoet\Util\Installation;
use MailPoet\Util\License\Features\CapabilitiesManager;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\Util\License\License;
use MailPoet\WooCommerce;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice as WPNotice;
use MailPoetVendor\Carbon\Carbon;
use Tracy\Debugger;

class PageRenderer {
  /** @var Bridge */
  private $bridge;

  /** @var Renderer */
  private $renderer;

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var FeaturesController */
  private $featuresController;

  /** @var Installation */
  private $installation;

  /** @var SettingsController */
  private $settings;

  /** @var UserFlagsController */
  private $userFlags;

  /** @var SegmentsRepository */
  private $segmentRepository;

  private $tagRepository;

  /** @var SubscribersCountCacheRecalculation */
  private $subscribersCountCacheRecalculation;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var TransientCache */
  private $transientCache;

  /** @var WPFunctions */
  private $wp;

  /*** @var AssetsController */
  private $assetsController;

  /** @var WooCommerce\Helper */
  private $wooCommerceHelper;

  /** @var WooCommerce\WooCommerceSubscriptions\Helper */
  private $wooCommerceSubscriptionsHelper;

  private CapabilitiesManager $capabilitiesManager;

  public function __construct(
    Bridge $bridge,
    Renderer $renderer,
    ServicesChecker $servicesChecker,
    FeaturesController $featuresController,
    Installation $installation,
    SettingsController $settings,
    UserFlagsController $userFlags,
    SegmentsRepository $segmentRepository,
    TagRepository $tagRepository,
    SubscribersCountCacheRecalculation $subscribersCountCacheRecalculation,
    SubscribersFeature $subscribersFeature,
    TrackingConfig $trackingConfig,
    TransientCache $transientCache,
    WPFunctions $wp,
    AssetsController $assetsController,
    WooCommerce\Helper $wooCommerceHelper,
    WooCommerce\WooCommerceSubscriptions\Helper $wooCommerceSubscriptionsHelper,
    CapabilitiesManager $capabilitiesManager
  ) {
    $this->bridge = $bridge;
    $this->renderer = $renderer;
    $this->servicesChecker = $servicesChecker;
    $this->featuresController = $featuresController;
    $this->installation = $installation;
    $this->settings = $settings;
    $this->userFlags = $userFlags;
    $this->segmentRepository = $segmentRepository;
    $this->tagRepository = $tagRepository;
    $this->subscribersCountCacheRecalculation = $subscribersCountCacheRecalculation;
    $this->subscribersFeature = $subscribersFeature;
    $this->trackingConfig = $trackingConfig;
    $this->transientCache = $transientCache;
    $this->wp = $wp;
    $this->assetsController = $assetsController;
    $this->wooCommerceHelper = $wooCommerceHelper;
    $this->wooCommerceSubscriptionsHelper = $wooCommerceSubscriptionsHelper;
    $this->capabilitiesManager = $capabilitiesManager;
  }

  /**
   * Set common data for template and display template
   * @param string $template
   * @param array $data
   */
  public function displayPage($template, array $data = []) {
    $installer = new Installer(Installer::PREMIUM_PLUGIN_SLUG);
    $premiumDownloadUrl = $this->subscribersFeature->hasValidPremiumKey()
      ? $installer->generatePluginDownloadUrl()
      : null;

    $lastAnnouncementDate = $this->settings->get('last_announcement_date');
    $lastAnnouncementSeen = $this->userFlags->get('last_announcement_seen');
    $wpSegment = $this->segmentRepository->getWPUsersSegment();
    $wpSegmentState = ($wpSegment instanceof SegmentEntity) && $wpSegment->getDeletedAt() === null ?
      SegmentEntity::SEGMENT_ENABLED : SegmentEntity::SEGMENT_DISABLED;
    $installedAtDiff = (new \DateTime($this->settings->get('installed_at')))->diff(new \DateTime());
    $subscriberCount = $this->subscribersFeature->getSubscribersCount();
    $subscribersCacheCreatedAt = Carbon::now();
    if ($this->subscribersFeature->isSubscribersCountEnoughForCache($subscriberCount)) {
      $subscribersCacheCreatedAt = $this->transientCache->getOldestCreatedAt(TransientCache::SUBSCRIBERS_STATISTICS_COUNT_KEY) ?: Carbon::now();
    }

    $defaults = [
      'current_page' => sanitize_text_field(wp_unslash($_GET['page'] ?? '')),
      'site_name' => $this->wp->wpSpecialcharsDecode($this->wp->getOption('blogname'), ENT_QUOTES),
      'main_page' => Menu::MAIN_PAGE_SLUG,
      'site_url' => $this->wp->siteUrl(),
      'site_address' => $this->wp->wpParseUrl($this->wp->homeUrl(), PHP_URL_HOST),
      'feature_flags' => $this->featuresController->getAllFlags(),
      'referral_id' => $this->settings->get(ReferralDetector::REFERRAL_SETTING_NAME),
      'mailpoet_api_key_state' => $this->settings->get('mta.mailpoet_api_key_state'),
      'mta_method' => $this->settings->get('mta.method'),
      'premium_key_state' => $this->settings->get('premium.premium_key_state'),
      'last_announcement_seen' => $lastAnnouncementSeen,
      'feature_announcement_has_news' => (empty($lastAnnouncementSeen) || $lastAnnouncementSeen < $lastAnnouncementDate),
      'wp_segment_state' => $wpSegmentState,
      'tracking_config' => $this->trackingConfig->getConfig(),
      'is_new_user' => $this->installation->isNewInstallation(),
      'installed_days_ago' => (int)$installedAtDiff->format('%a'),
      'deactivate_subscriber_after_inactive_days' => $this->settings->get('deactivate_subscriber_after_inactive_days'),
      'send_transactional_emails' => (bool)$this->settings->get('send_transactional_emails'),
      'transactional_emails_opt_in_notice_dismissed' => (bool)$this->userFlags->get('transactional_emails_opt_in_notice_dismissed'),
      'track_wizard_loaded_via_woocommerce' => (bool)$this->settings->get(WelcomeWizard::TRACK_LOADDED_VIA_WOOCOMMERCE_SETTING_NAME),
      'track_wizard_loaded_via_woocommerce_marketing_dashboard' => (bool)$this->settings->get(WelcomeWizard::TRACK_LOADDED_VIA_WOOCOMMERCE_MARKETING_DASHBOARD_SETTING_NAME),
      'mail_function_enabled' => function_exists('mail') && is_callable('mail'),
      'admin_plugins_url' => WPFunctions::get()->adminUrl('plugins.php'),

      // Premium & plan upgrade info
      'current_wp_user_email' => $this->wp->wpGetCurrentUser()->user_email,
      'link_premium' => $this->wp->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-upgrade'),
      'premium_plugin_installed' => Installer::isPluginInstalled(Installer::PREMIUM_PLUGIN_SLUG),
      'premium_plugin_active' => $this->servicesChecker->isPremiumPluginActive(),
      'premium_plugin_download_url' => $premiumDownloadUrl,
      'premium_plugin_activation_url' => $installer->generatePluginActivationUrl(Installer::PREMIUM_PLUGIN_PATH),
      'has_valid_api_key' => $this->subscribersFeature->hasValidApiKey(),
      'has_valid_premium_key' => $this->subscribersFeature->hasValidPremiumKey(),
      'has_premium_support' => $this->subscribersFeature->hasPremiumSupport(),
      'has_mss_key_specified' => Bridge::isMSSKeySpecified(),
      'mss_key_invalid' => $this->servicesChecker->isMailPoetAPIKeyValid() === false,
      'mss_key_valid' => $this->subscribersFeature->hasValidMssKey(),
      'mss_key_pending_approval' => $this->servicesChecker->isMailPoetAPIKeyPendingApproval(),
      'mss_active' => $this->bridge->isMailpoetSendingServiceEnabled(),
      'plugin_partial_key' => $this->servicesChecker->generatePartialApiKey(),
      'subscriber_count' => $subscriberCount,
      'subscribers_counts_cache_created_at' => $subscribersCacheCreatedAt->format('Y-m-d\TH:i:sO'),
      'subscribers_limit' => $this->subscribersFeature->getSubscribersLimit(),
      'subscribers_limit_reached' => $this->subscribersFeature->check(),
      'email_volume_limit' => $this->subscribersFeature->getEmailVolumeLimit(),
      'email_volume_limit_reached' => $this->subscribersFeature->checkEmailVolumeLimitIsReached(),
      'capabilities' => $this->capabilitiesManager->getCapabilities(),
      'tier' => $this->capabilitiesManager->getTier(),
      'urls' => [
        'automationListing' => admin_url('admin.php?page=mailpoet-automation'),
        'automationEditor' => admin_url('admin.php?page=mailpoet-automation-editor'),
        'automationTemplates' => admin_url('admin.php?page=mailpoet-automation-templates'),
        'automationAnalytics' => admin_url('admin.php?page=mailpoet-automation-analytics'),
      ],
      'woocommerce_store_config' => $this->wooCommerceHelper->isWooCommerceActive() ? $this->getWoocommerceStoreConfig() : null,
      'tags' => array_map(function (TagEntity $tag): array {
        return [
        'id' => $tag->getId(),
        'name' => $tag->getName(),
        ];
      }, $this->tagRepository->findAll()),
      'display_docsbot_widget' => $this->displayDocsBotWidget(),
      'is_woocommerce_subscriptions_active' => $this->wooCommerceSubscriptionsHelper->isWooCommerceSubscriptionsActive(),
      'cron_trigger_method' => $this->settings->get('cron_trigger.method'),
    ];

    if (!$defaults['premium_plugin_active']) {
      $defaults['free_premium_subscribers_limit'] = License::FREE_PREMIUM_SUBSCRIBERS_LIMIT;
    }

    try {
      if (
        class_exists(Debugger::class)
        && class_exists(DIPanel::class)
      ) {
        DIPanel::init();
      }
      if (is_admin() && $this->subscribersCountCacheRecalculation->shouldBeScheduled()) {
        $this->subscribersCountCacheRecalculation->schedule();
      }

      // If the page didn't enqueue any assets, this will act as a fallback.
      // If some assets were enqueued, this won't change the queue ordering.
      $this->assetsController->setupAdminPagesDependencies();
      $this->wp->doAction('mailpoet_styles_admin_after');

      // We are in control of the template and the data can be considered safe at this point
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
      echo $this->renderer->render($template, $data + $defaults);
    } catch (\Exception $e) {
      $notice = new WPNotice(WPNotice::TYPE_ERROR, $e->getMessage());
      $notice->displayWPNotice();
    }
  }

  private function getWoocommerceStoreConfig() {

    return [
      'precision' => $this->wooCommerceHelper->wcGetPriceDecimals(),
      'decimalSeparator' => $this->wooCommerceHelper->wcGetPriceDecimalSeperator(),
      'thousandSeparator' => $this->wooCommerceHelper->wcGetPriceThousandSeparator(),
      'code' => $this->wooCommerceHelper->getWoocommerceCurrency(),
      'symbol' => html_entity_decode($this->wooCommerceHelper->getWoocommerceCurrencySymbol()),
      'symbolPosition' => $this->wp->getOption('woocommerce_currency_pos'),
      'priceFormat' => $this->wooCommerceHelper->getWoocommercePriceFormat(),

    ];
  }

  public function displayDocsBotWidget(): bool {
    $display = $this->wp->applyFilters('mailpoet_display_docsbot_widget', $this->settings->get('3rd_party_libs.enabled') === '1');
    return (bool)$display;
  }
}
