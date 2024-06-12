<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\API;
use MailPoet\API\REST\API as RestApi;
use MailPoet\AutomaticEmails\AutomaticEmails;
use MailPoet\Automation\Engine\Engine;
use MailPoet\Automation\Engine\Hooks as AutomationHooks;
use MailPoet\Automation\Integrations\MailPoet\MailPoetIntegration;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerceIntegration;
use MailPoet\Cron\CronTrigger;
use MailPoet\Cron\DaemonActionSchedulerRunner;
use MailPoet\EmailEditor\Engine\EmailEditor;
use MailPoet\EmailEditor\Integrations\Core\Initializer as CoreEmailEditorIntegration;
use MailPoet\EmailEditor\Integrations\MailPoet\Blocks\BlockTypesController;
use MailPoet\EmailEditor\Integrations\MailPoet\EmailEditor as MailpoetEmailEditorIntegration;
use MailPoet\Features\FeaturesController;
use MailPoet\InvalidStateException;
use MailPoet\Migrator\Cli as MigratorCli;
use MailPoet\PostEditorBlocks\PostEditorBlock;
use MailPoet\PostEditorBlocks\WooCommerceBlocksIntegration;
use MailPoet\Router;
use MailPoet\Settings\SettingsController;
use MailPoet\Statistics\Track\SubscriberActivityTracker;
use MailPoet\Util\ConflictResolver;
use MailPoet\Util\Helpers;
use MailPoet\Util\Notices\PermanentNotices;
use MailPoet\Util\Url;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WooCommerce\TransactionalEmailHooks as WCTransactionalEmails;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice as WPNotice;

class Initializer {
  /** @var AccessControl */
  private $accessControl;

  /** @var Renderer */
  private $renderer;

  /** @var RendererFactory */
  private $rendererFactory;

  /** @var API */
  private $api;

  /** @var RestApi */
  private $restApi;

  /** @var Activator */
  private $activator;

  /** @var SettingsController */
  private $settings;

  /** @var MigratorCli */
  private $migratorCli;

  /** @var Router\Router */
  private $router;

  /** @var Hooks */
  private $hooks;

  /** @var Changelog */
  private $changelog;

  /** @var Menu */
  private $menu;

  /** @var CronTrigger */
  private $cronTrigger;

  /** @var PermanentNotices */
  private $permanentNotices;

  /** @var Shortcodes */
  private $shortcodes;

  /** @var DatabaseInitializer */
  private $databaseInitializer;

  /** @var WCTransactionalEmails */
  private $wcTransactionalEmails;

  /** @var WooCommerceHelper */
  private $wcHelper;

  /** @var \MailPoet\PostEditorBlocks\PostEditorBlock */
  private $postEditorBlock;

  /** @var \MailPoet\PostEditorBlocks\WooCommerceBlocksIntegration */
  private $woocommerceBlocksIntegration;

  /** @var Localizer */
  private $localizer;

  /** @var AutomaticEmails */
  private $automaticEmails;

  /** @var WPFunctions */
  private $wpFunctions;

  /** @var AssetsLoader */
  private $assetsLoader;

  /** @var SubscriberActivityTracker */
  private $subscriberActivityTracker;

  /** @var Engine */
  private $automationEngine;

  /** @var MailPoetIntegration */
  private $automationMailPoetIntegration;

  /** @var WooCommerceIntegration */
  private $woocommerceIntegration;

  /** @var PersonalDataExporters */
  private $personalDataExporters;

  /** @var DaemonActionSchedulerRunner */
  private $actionSchedulerRunner;

  /** @var EmailEditor */
  private $emailEditor;

  /** @var MailpoetEmailEditorIntegration */
  private $mailpoetEmailEditorIntegration;

  /** @var CoreEmailEditorIntegration */
  private $coreEmailEditorIntegration;

  /** @var BlockTypesController */
  private $blockTypesController;

  /** @var FeaturesController */
  private $featureController;

  /** @var Url */
  private $urlHelper;

  const INITIALIZED = 'MAILPOET_INITIALIZED';

  const PLUGIN_ACTIVATED = 'mailpoet_plugin_activated';

  public function __construct(
    RendererFactory $rendererFactory,
    AccessControl $accessControl,
    API $api,
    RestApi $restApi,
    Activator $activator,
    SettingsController $settings,
    MigratorCli $migratorCli,
    Router\Router $router,
    Hooks $hooks,
    Changelog $changelog,
    Menu $menu,
    CronTrigger $cronTrigger,
    PermanentNotices $permanentNotices,
    Shortcodes $shortcodes,
    DatabaseInitializer $databaseInitializer,
    WCTransactionalEmails $wcTransactionalEmails,
    PostEditorBlock $postEditorBlock,
    WooCommerceBlocksIntegration $woocommerceBlocksIntegration,
    WooCommerceHelper $wcHelper,
    Localizer $localizer,
    AutomaticEmails $automaticEmails,
    SubscriberActivityTracker $subscriberActivityTracker,
    WPFunctions $wpFunctions,
    AssetsLoader $assetsLoader,
    Engine $automationEngine,
    MailPoetIntegration $automationMailPoetIntegration,
    WooCommerceIntegration $woocommerceIntegration,
    PersonalDataExporters $personalDataExporters,
    DaemonActionSchedulerRunner $actionSchedulerRunner,
    EmailEditor $emailEditor,
    BlockTypesController $blockTypesController,
    MailpoetEmailEditorIntegration $mailpoetEmailEditorIntegration,
    CoreEmailEditorIntegration $coreEmailEditorIntegration,
    FeaturesController $featureController,
    Url $urlHelper
  ) {
    $this->rendererFactory = $rendererFactory;
    $this->accessControl = $accessControl;
    $this->api = $api;
    $this->restApi = $restApi;
    $this->activator = $activator;
    $this->settings = $settings;
    $this->migratorCli = $migratorCli;
    $this->router = $router;
    $this->hooks = $hooks;
    $this->changelog = $changelog;
    $this->menu = $menu;
    $this->cronTrigger = $cronTrigger;
    $this->permanentNotices = $permanentNotices;
    $this->shortcodes = $shortcodes;
    $this->databaseInitializer = $databaseInitializer;
    $this->wcTransactionalEmails = $wcTransactionalEmails;
    $this->wcHelper = $wcHelper;
    $this->postEditorBlock = $postEditorBlock;
    $this->woocommerceBlocksIntegration = $woocommerceBlocksIntegration;
    $this->localizer = $localizer;
    $this->automaticEmails = $automaticEmails;
    $this->subscriberActivityTracker = $subscriberActivityTracker;
    $this->wpFunctions = $wpFunctions;
    $this->assetsLoader = $assetsLoader;
    $this->automationEngine = $automationEngine;
    $this->automationMailPoetIntegration = $automationMailPoetIntegration;
    $this->woocommerceIntegration = $woocommerceIntegration;
    $this->personalDataExporters = $personalDataExporters;
    $this->actionSchedulerRunner = $actionSchedulerRunner;
    $this->emailEditor = $emailEditor;
    $this->mailpoetEmailEditorIntegration = $mailpoetEmailEditorIntegration;
    $this->coreEmailEditorIntegration = $coreEmailEditorIntegration;
    $this->blockTypesController = $blockTypesController;
    $this->featureController = $featureController;
    $this->urlHelper = $urlHelper;
  }

  public function init() {
    // Initialize Action Scheduler. It needs to be called early because it hooks into `plugins_loaded`.
    require_once __DIR__ . '/../../vendor/woocommerce/action-scheduler/action-scheduler.php';

    // load translations and setup translations update/download
    $this->setupLocalizer();

    try {
      $this->databaseInitializer->initializeConnection();
    } catch (\Exception $e) {
      return WPNotice::displayError(Helpers::replaceLinkTags(
        __('Unable to connect to the database (the database is unable to open a file or folder), the connection is likely not configured correctly. Please read our [link] Knowledge Base article [/link] for steps how to resolve it.', 'mailpoet'),
        'https://kb.mailpoet.com/article/200-solving-database-connection-issues',
        [
          'target' => '_blank',
        ]
      ));
    }

    // activation function
    $this->wpFunctions->registerActivationHook(
      Env::$file,
      [
        $this,
        'runActivator',
      ]
    );

    // deactivation function
    $this->wpFunctions->registerDeactivationHook(
      Env::$file,
      [
        $this,
        'runDeactivation',
      ]
    );

    $this->wpFunctions->addAction('activated_plugin', [
      new PluginActivatedHook(new DeferredAdminNotices),
      'action',
    ], 10, 2);

    $this->wpFunctions->addAction('plugins_loaded', [
      $this,
      'pluginsLoaded',
    ], 0);

    $this->wpFunctions->addAction('init', [
      $this,
      'preInitialize',
    ], 0);

    $this->wpFunctions->addAction('init', [
      $this,
      'initialize',
    ]);

    $this->wpFunctions->addAction(
      'init',
      [$this, 'maybeRunActivator'],
      PHP_INT_MIN
    );

    $this->wpFunctions->addAction('admin_init', [
      $this,
      'setupPrivacyPolicy',
    ]);

    $this->wpFunctions->addAction('wp_loaded', [
      $this,
      'postInitialize',
    ]);

    $this->wpFunctions->addAction('admin_init', [
      new DeferredAdminNotices,
      'printAndClean',
    ]);

    $this->wpFunctions->addFilter('wpmu_drop_tables', [
      $this,
      'multisiteDropTables',
    ]);

    $this->wpFunctions->addFilter('mailpoet_email_editor_initialized', [
      $this,
      'setupEmailEditorIntegrations',
    ]);

    WPFunctions::get()->addAction(AutomationHooks::INITIALIZE, [
      $this->automationMailPoetIntegration,
      'register',
    ]);
    WPFunctions::get()->addAction(AutomationHooks::INITIALIZE, [
      $this->woocommerceIntegration,
      'register',
    ]);

    WPFunctions::get()->addAction('admin_init', [
      $this,
      'afterPluginActivation',
    ]);

    $this->hooks->initEarlyHooks();
  }

  public function runActivator() {
    try {
      $this->wpFunctions->addOption(self::PLUGIN_ACTIVATED, true); // used in afterPluginActivation
      $this->activator->activate();
    } catch (InvalidStateException $e) {
      return $this->handleRunningMigration($e);
    } catch (\Exception $e) {
      return $this->handleFailedInitialization($e);
    }
  }

  public function pluginsLoaded() {
    $this->hooks->init();
  }

  public function preInitialize() {
    try {
      $this->renderer = $this->rendererFactory->getRenderer();
      $this->setupWidget();
      $this->setupWoocommerceTransactionalEmails();
      $this->assetsLoader->loadStyles();
    } catch (\Exception $e) {
      $this->handleFailedInitialization($e);
    }
  }

  public function setupWidget() {
    $this->wpFunctions->registerWidget('\MailPoet\Form\Widget');
  }

  public function initialize() {
    try {
      $this->migratorCli->initialize();
      $this->setupInstaller();
      $this->setupUpdater();

      $this->setupCapabilities();
      $this->menu->init();
      $this->setupShortcodes();
      $this->setupImages();
      $this->setupPersonalDataExporters();
      $this->setupPersonalDataErasers();

      $this->changelog->init();
      $this->setupCronTrigger();
      $this->setupConflictResolver();

      $this->setupPages();

      $this->setupPermanentNotices();
      $this->setupAutomaticEmails();
      $this->setupWoocommerceBlocksIntegration();
      $this->setupDeactivationPoll();
      $this->subscriberActivityTracker->trackActivity();
      $this->postEditorBlock->init();
      $this->automationEngine->initialize();
      if ($this->featureController->isSupported(FeaturesController::GUTENBERG_EMAIL_EDITOR)) {
        $this->blockTypesController->initialize();
        $this->emailEditor->initialize();
      }
      $this->wpFunctions->doAction('mailpoet_initialized', MAILPOET_VERSION);
    } catch (InvalidStateException $e) {
      return $this->handleRunningMigration($e);
    } catch (\Exception $e) {
      return $this->handleFailedInitialization($e);
    }

    define(self::INITIALIZED, true);
  }

  /**
   * Walk around for getting this to work correctly
   *
   * Read more here: https://developer.wordpress.org/reference/functions/register_activation_hook/
   * and https://github.com/mailpoet/mailpoet/pull/4620#discussion_r1058210174
   * @return void
   */
  public function afterPluginActivation() {
    if (!$this->wpFunctions->isAdmin() || !defined(self::INITIALIZED) || !$this->wpFunctions->getOption(self::PLUGIN_ACTIVATED)) return;

    $currentUrl = $this->urlHelper->getCurrentUrl();

    // wp automatically redirect to `wp-admin/plugins.php?activate=true&...` after plugin activation
    $activatedByWpAdmin = !empty(strpos($currentUrl, 'plugins.php')) && isset($_GET['activate']) && (bool)$_GET['activate'];

    // We want to run this only once immediately after activation.
    // Delete the flag to prevent triggering on subsequent page loads.
    $this->wpFunctions->deleteOption(self::PLUGIN_ACTIVATED);

    // If not activated by wp. Do not redirect e.g WooCommerce NUX
    if ($activatedByWpAdmin) {
      $this->changelog->redirectToLandingPage();
    }
  }

  /**
   * Checks if the plugin was updated and runs the activator if needed. The activator
   * will run the database migrations and update the db version among a few other things.
   *
   * @return void
   * @throws InvalidStateException
   */
  public function maybeRunActivator() {
    try {
      $currentDbVersion = $this->settings->get('db_version');
    } catch (\Exception $e) {
      $currentDbVersion = null;
    }

    if (version_compare((string)$currentDbVersion, Env::$version) === 0) {
      return;
    }

    // if current db version and plugin version differ
    try {
      $this->activator->activate();
    } catch (InvalidStateException $e) {
      $this->handleRunningMigration($e);
    } catch (\Exception $e) {
      $this->handleFailedInitialization($e);
    }
  }

  public function setupInstaller() {
    $installer = new Installer(
      Installer::PREMIUM_PLUGIN_SLUG
    );
    $installer->init();
  }

  public function setupUpdater() {
    $premiumSlug = Installer::PREMIUM_PLUGIN_SLUG;
    $premiumPluginFile = Installer::getPluginFile($premiumSlug);
    $premiumVersion = defined('MAILPOET_PREMIUM_VERSION') ? MAILPOET_PREMIUM_VERSION : null;

    if (empty($premiumPluginFile) || !$premiumVersion) {
      return false;
    }
    $updater = new Updater(
      $premiumPluginFile,
      $premiumSlug,
      MAILPOET_PREMIUM_VERSION
    );
    $updater->init();
  }

  public function setupLocalizer() {
    $this->localizer->init($this->wpFunctions);
  }

  public function setupCapabilities() {
    $caps = new Capabilities($this->renderer);
    $caps->init();
  }

  public function setupShortcodes() {
    $this->shortcodes->init();
  }

  public function setupImages() {
    $this->wpFunctions->addImageSize('mailpoet_newsletter_max', Env::NEWSLETTER_CONTENT_WIDTH);
  }

  public function setupCronTrigger() {
    $this->cronTrigger->init((string)php_sapi_name());
  }

  public function setupConflictResolver() {
    $conflictResolver = new ConflictResolver();
    $conflictResolver->init();
  }

  public function postInitialize() {
    if (!defined(self::INITIALIZED)) return;
    try {
      $this->api->init();
      $this->restApi->init();
      $this->router->init();
      $this->setupUserLocale();
    } catch (\Exception $e) {
      $this->handleFailedInitialization($e);
    }
  }

  public function setupUserLocale() {
    if (get_user_locale() === $this->wpFunctions->getLocale()) return;
    $this->wpFunctions->unloadTextdomain(Env::$pluginName);
    $this->localizer->init($this->wpFunctions);
  }

  public function setupPages() {
    $pages = new \MailPoet\Settings\Pages();
    $pages->init();
  }

  public function setupPrivacyPolicy() {
    $privacyPolicy = new PrivacyPolicy();
    $privacyPolicy->init();
  }

  public function setupPersonalDataExporters() {
    $this->personalDataExporters->init();
  }

  public function setupPersonalDataErasers() {
    $erasers = new PersonalDataErasers();
    $erasers->init();
  }

  public function setupPermanentNotices() {
    $this->permanentNotices->init();
  }

  public function handleFailedInitialization($exception) {
    // check if we are able to add pages at this point
    if (function_exists('wp_get_current_user')) {
      Menu::addErrorPage($this->accessControl);
    }
    return WPNotice::displayError($exception);
  }

  private function handleRunningMigration(InvalidStateException $exception) {
    if (function_exists('wp_get_current_user')) {
      Menu::addErrorPage($this->accessControl);
    }
    return WPNotice::displayWarning($exception->getMessage());
  }

  public function setupAutomaticEmails() {
    $this->automaticEmails->init();
    $this->automaticEmails->getAutomaticEmails();
  }

  public function multisiteDropTables($tables) {
    global $wpdb;
    $tablePrefix = $wpdb->prefix . Env::$pluginPrefix;
    $mailpoetTables = $wpdb->get_col(
      $wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $wpdb->esc_like($tablePrefix) . '%'
      )
    );
    return array_merge($tables, $mailpoetTables);
  }

  public function setupEmailEditorIntegrations() {
    $this->mailpoetEmailEditorIntegration->initialize();
    $this->coreEmailEditorIntegration->initialize();
  }

  public function runDeactivation() {
    $this->actionSchedulerRunner->deactivate();
  }

  private function setupWoocommerceTransactionalEmails() {
    $wcEnabled = $this->wcHelper->isWooCommerceActive();
    $optInEnabled = $this->settings->get('woocommerce.use_mailpoet_editor', false);
    if ($wcEnabled && $optInEnabled) {
      $this->wcTransactionalEmails->overrideStylesForWooEmails();
      $this->wcTransactionalEmails->useTemplateForWoocommerceEmails();
    }
  }

  private function setupWoocommerceBlocksIntegration() {
    $wcEnabled = $this->wcHelper->isWooCommerceActive();
    $wcBlocksEnabled = $this->wcHelper->isWooCommerceBlocksActive('8.0.0');
    if ($wcEnabled && $wcBlocksEnabled) {
      $this->woocommerceBlocksIntegration->init();
    }
  }

  private function setupDeactivationPoll(): void {
    $deactivationPoll = new DeactivationPoll($this->wpFunctions, $this->renderer);
    $deactivationPoll->init();
  }
}
