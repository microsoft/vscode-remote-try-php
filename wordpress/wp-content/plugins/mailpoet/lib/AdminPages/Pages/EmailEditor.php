<?php declare(strict_types = 1);

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\API;
use MailPoet\Config\Env;
use MailPoet\Config\Installer;
use MailPoet\Config\ServicesChecker;
use MailPoet\EmailEditor\Engine\SettingsController;
use MailPoet\EmailEditor\Engine\ThemeController;
use MailPoet\EmailEditor\Integrations\MailPoet\EmailEditor as EditorInitController;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Settings\SettingsController as MailPoetSettings;
use MailPoet\Util\CdnAssetUrl;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\WP\Functions as WPFunctions;

class EmailEditor {
  private WPFunctions $wp;

  private SettingsController $settingsController;

  private ThemeController $themeController;

  private CdnAssetUrl $cdnAssetUrl;

  private ServicesChecker $servicesChecker;

  private SubscribersFeature $subscribersFeature;

  private MailPoetSettings $mailpoetSettings;

  private NewslettersRepository $newslettersRepository;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settingsController,
    CdnAssetUrl $cdnAssetUrl,
    ServicesChecker $servicesChecker,
    SubscribersFeature $subscribersFeature,
    ThemeController $themeController,
    MailPoetSettings $mailpoetSettings,
    NewslettersRepository $newslettersRepository
  ) {
    $this->wp = $wp;
    $this->settingsController = $settingsController;
    $this->cdnAssetUrl = $cdnAssetUrl;
    $this->servicesChecker = $servicesChecker;
    $this->subscribersFeature = $subscribersFeature;
    $this->themeController = $themeController;
    $this->mailpoetSettings = $mailpoetSettings;
    $this->newslettersRepository = $newslettersRepository;
  }

  public function render() {
    $postId = isset($_GET['postId']) ? intval($_GET['postId']) : 0;
    $post = $this->wp->getPost($postId);
    if (!$post instanceof \WP_Post || $post->post_type !== EditorInitController::MAILPOET_EMAIL_POST_TYPE) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      return;
    }

    $assetsParams = require_once Env::$assetsPath . '/dist/js/email-editor/email_editor.asset.php';
    $this->wp->wpEnqueueScript(
      'mailpoet_email_editor',
      Env::$assetsUrl . '/dist/js/email-editor/email_editor.js',
      $assetsParams['dependencies'],
      $assetsParams['version'],
      true
    );
    $this->wp->wpEnqueueStyle(
      'mailpoet_email_editor',
      Env::$assetsUrl . '/dist/js/email-editor/email_editor.css',
      [],
      $assetsParams['version']
    );

    $jsonAPIRoot = rtrim($this->wp->escUrlRaw(admin_url('admin-ajax.php')), '/');
    $token = $this->wp->wpCreateNonce('mailpoet_token');
    $apiVersion = API::CURRENT_VERSION;
    $currentUserEmail = $this->wp->wpGetCurrentUser()->user_email;
    $this->wp->wpLocalizeScript(
      'mailpoet_email_editor',
      'MailPoetEmailEditor',
      [
        'json_api_root' => esc_js($jsonAPIRoot),
        'api_token' => esc_js($token),
        'api_version' => esc_js($apiVersion),
        'cdn_url' => esc_js($this->cdnAssetUrl->generateCdnUrl("")),
        'is_premium_plugin_active' => (bool)$this->servicesChecker->isPremiumPluginActive(),
        'current_wp_user_email' => esc_js($currentUserEmail),
        'editor_settings' => $this->settingsController->getSettings(),
        'editor_layout' => $this->settingsController->getLayout(),
        'editor_theme' => $this->themeController->getTheme()->get_raw_data(),
        'urls' => [
          'listings' => admin_url('admin.php?page=mailpoet-newsletters'),
        ],
      ]
    );

    // Renders additional script data that some components require e.g. PremiumModal. This is done here instead of using
    // PageRenderer since that introduces other dependencies we want to avoid. Used by getUpgradeInfo.
    $installer = new Installer(Installer::PREMIUM_PLUGIN_SLUG);
    $inline_script_data = [
      'mailpoet_premium_plugin_installed' => Installer::isPluginInstalled(Installer::PREMIUM_PLUGIN_SLUG),
      'mailpoet_premium_plugin_active' => $this->servicesChecker->isPremiumPluginActive(),
      'mailpoet_premium_plugin_download_url' => $this->subscribersFeature->hasValidPremiumKey() ? $installer->generatePluginDownloadUrl() : null,
      'mailpoet_premium_plugin_activation_url' => $installer->generatePluginActivationUrl(Installer::PREMIUM_PLUGIN_PATH),
      'mailpoet_has_valid_api_key' => $this->subscribersFeature->hasValidApiKey(),
      'mailpoet_has_valid_premium_key' => $this->subscribersFeature->hasValidPremiumKey(),
      'mailpoet_has_premium_support' => $this->subscribersFeature->hasPremiumSupport(),
      'mailpoet_plugin_partial_key' => $this->servicesChecker->generatePartialApiKey(),
      'mailpoet_subscribers_count' => $this->subscribersFeature->getSubscribersCount(),
      'mailpoet_subscribers_limit' => $this->subscribersFeature->getSubscribersLimit(),
      'mailpoet_subscribers_limit_reached' => $this->subscribersFeature->check(),
      // settings needed for Satismeter tracking
      'mailpoet_3rd_party_libs_enabled' => $this->mailpoetSettings->get('3rd_party_libs.enabled') === '1',
      'mailpoet_display_nps_email_editor' => $this->newslettersRepository->getCountOfEmailsWithWPPost() > 1, // Poll should be displayed only if there are 2 and more emails
      'mailpoet_display_nps_poll' => true,
      'mailpoet_current_wp_user' => $this->wp->wpGetCurrentUser()->to_array(),
      'mailpoet_current_wp_user_firstname' => $this->wp->wpGetCurrentUser()->user_firstname,
      'mailpoet_site_url' => $this->wp->siteUrl(),
    ];
    $this->wp->wpAddInlineScript('mailpoet_email_editor', implode('', array_map(function ($key) use ($inline_script_data) {
      return sprintf("var %s=%s;", $key, json_encode($inline_script_data[$key]));
    }, array_keys($inline_script_data))), 'before');

    // Load CSS from Post Editor
    $this->wp->wpEnqueueStyle('wp-edit-post');
    // Load CSS for the format library - used for example in popover
    $this->wp->wpEnqueueStyle('wp-format-library');

    // Enqueue media library scripts
    $this->wp->wpEnqueueMedia();

    echo '<div id="mailpoet-email-editor" class="block-editor"></div>';
  }
}
