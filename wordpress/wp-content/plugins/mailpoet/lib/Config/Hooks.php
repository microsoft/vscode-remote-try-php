<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronTrigger;
use MailPoet\Form\DisplayFormInWPContent;
use MailPoet\Mailer\WordPress\WordpressMailerReplacer;
use MailPoet\Newsletter\Scheduler\PostNotificationScheduler;
use MailPoet\Segments\WP;
use MailPoet\Settings\SettingsController;
use MailPoet\Statistics\Track\SubscriberHandler;
use MailPoet\Subscription\Comment;
use MailPoet\Subscription\Form;
use MailPoet\Subscription\Manage;
use MailPoet\Subscription\Registration;
use MailPoet\WooCommerce\Integrations\AutomateWooHooks;
use MailPoet\WooCommerce\WooSystemInfoController;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WPCOM\DotcomLicenseProvisioner;

class Hooks {
  /** @var Form */
  private $subscriptionForm;

  /** @var Comment */
  private $subscriptionComment;

  /** @var Manage */
  private $subscriptionManage;

  /** @var Registration */
  private $subscriptionRegistration;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var PostNotificationScheduler */
  private $postNotificationScheduler;

  /** @var WordpressMailerReplacer */
  private $wordpressMailerReplacer;

  /** @var DisplayFormInWPContent */
  private $displayFormInWPContent;

  /** @var WP */
  private $wpSegment;

  /** @var SubscriberHandler */
  private $subscriberHandler;

  /** @var HooksWooCommerce */
  private $hooksWooCommerce;

  /** @var SubscriberChangesNotifier */
  private $subscriberChangesNotifier;

  /** @var DotcomLicenseProvisioner */
  private $dotcomLicenseProvisioner;

  /** @var AutomateWooHooks */
  private $automateWooHooks;

  /** @var WooSystemInfoController */
  private $wooSystemInfoController;

  /** @var CronTrigger */
  private $cronTrigger;

  public function __construct(
    Form $subscriptionForm,
    Comment $subscriptionComment,
    Manage $subscriptionManage,
    Registration $subscriptionRegistration,
    SettingsController $settings,
    WPFunctions $wp,
    PostNotificationScheduler $postNotificationScheduler,
    WordpressMailerReplacer $wordpressMailerReplacer,
    DisplayFormInWPContent $displayFormInWPContent,
    HooksWooCommerce $hooksWooCommerce,
    SubscriberHandler $subscriberHandler,
    SubscriberChangesNotifier $subscriberChangesNotifier,
    WP $wpSegment,
    DotcomLicenseProvisioner $dotcomLicenseProvisioner,
    AutomateWooHooks $automateWooHooks,
    WooSystemInfoController $wooSystemInfoController,
    CronTrigger $cronTrigger
  ) {
    $this->subscriptionForm = $subscriptionForm;
    $this->subscriptionComment = $subscriptionComment;
    $this->subscriptionManage = $subscriptionManage;
    $this->subscriptionRegistration = $subscriptionRegistration;
    $this->settings = $settings;
    $this->wp = $wp;
    $this->postNotificationScheduler = $postNotificationScheduler;
    $this->wordpressMailerReplacer = $wordpressMailerReplacer;
    $this->displayFormInWPContent = $displayFormInWPContent;
    $this->wpSegment = $wpSegment;
    $this->subscriberHandler = $subscriberHandler;
    $this->hooksWooCommerce = $hooksWooCommerce;
    $this->subscriberChangesNotifier = $subscriberChangesNotifier;
    $this->dotcomLicenseProvisioner = $dotcomLicenseProvisioner;
    $this->automateWooHooks = $automateWooHooks;
    $this->wooSystemInfoController = $wooSystemInfoController;
    $this->cronTrigger = $cronTrigger;
  }

  public function init() {
    $this->setupWPUsers();
    $this->setupWooCommerceUsers();
    $this->setupWooCommercePurchases();
    $this->setupWooCommerceSubscriberEngagement();
    $this->setupWooCommerceTracking();
    $this->setupListing();
    $this->setupSubscriptionEvents();
    $this->setupWooCommerceSubscriptionEvents();
    $this->setupAutomateWooSubscriptionEvents();
    $this->setupPostNotifications();
    $this->setupWooCommerceSettings();
    $this->setupWoocommerceSystemInfo();
    $this->setupFooter();
    $this->setupSettingsLinkInPluginPage();
    $this->setupChangeNotifications();
    $this->setupLicenseProvisioning();
    $this->deactivateMailPoetCronBeforePluginUpgrade();
  }

  public function initEarlyHooks() {
    $this->setupMailer();
  }

  public function setupSubscriptionEvents() {

    $subscribe = $this->settings->get('subscribe', []);
    // Subscribe in comments
    if (
      isset($subscribe['on_comment']['enabled'])
      &&
      (bool)$subscribe['on_comment']['enabled']
    ) {
      if ($this->wp->isUserLoggedIn()) {
        $this->wp->addAction(
          'comment_form_field_comment',
          [$this->subscriptionComment, 'extendLoggedInForm']
        );
      } else {
        $this->wp->addAction(
          'comment_form_after_fields',
          [$this->subscriptionComment, 'extendLoggedOutForm']
        );
      }

      $this->wp->addAction(
        'comment_post',
        [$this->subscriptionComment, 'onSubmit'],
        60,
        2
      );

      $this->wp->addAction(
        'wp_set_comment_status',
        [$this->subscriptionComment, 'onStatusUpdate'],
        60,
        2
      );
    }

    // Subscribe in registration form
    if (
      isset($subscribe['on_register']['enabled'])
      &&
      (bool)$subscribe['on_register']['enabled']
    ) {
      if (is_multisite()) {
        $this->wp->addAction(
          'signup_extra_fields',
          [$this->subscriptionRegistration, 'extendForm']
        );
        $this->wp->addAction(
          'wpmu_validate_user_signup',
          [$this->subscriptionRegistration, 'onMultiSiteRegister'],
          60,
          1
        );
      } else {
        $this->wp->addAction(
          'register_form',
          [$this->subscriptionRegistration, 'extendForm']
        );
        // we need to process new users while they are registered.
        // We used `register_post` before but that is too soon
        //   because if registration fails during `registration_errors` we will keep the user as subscriber.
        // So we are hooking to `registration_error` with a low priority.
        $this->wp->addFilter(
          'registration_errors',
          [$this->subscriptionRegistration, 'onRegister'],
          60,
          3
        );
      }
      $this->wp->addAction(
        'woocommerce_register_form',
        [$this->hooksWooCommerce, 'extendForm']
      );
      $this->wp->addFilter(
        'woocommerce_registration_errors',
        [$this->hooksWooCommerce, 'onRegister'],
        60,
        3
      );
    }

    // Manage subscription
    $this->wp->addAction(
      'admin_post_mailpoet_subscription_update',
      [$this->subscriptionManage, 'onSave']
    );
    $this->wp->addAction(
      'admin_post_nopriv_mailpoet_subscription_update',
      [$this->subscriptionManage, 'onSave']
    );

    // Subscription form
    $this->wp->addAction(
      'admin_post_mailpoet_subscription_form',
      [$this->subscriptionForm, 'onSubmit']
    );
    $this->wp->addAction(
      'admin_post_nopriv_mailpoet_subscription_form',
      [$this->subscriptionForm, 'onSubmit']
    );
    $this->wp->addFilter(
      'the_content',
      [$this->displayFormInWPContent, 'contentDisplay']
    );
    $this->wp->addFilter(
      'woocommerce_product_loop_end',
      [$this->displayFormInWPContent, 'wooProductListDisplay']
    );
    $this->wp->addAction(
      'wp_footer',
      [$this->displayFormInWPContent, 'maybeRenderFormsInFooter']
    );
  }

  public function setupMailer() {
    $this->wp->addAction('plugins_loaded', [
      $this->wordpressMailerReplacer,
      'replaceWordPressMailer',
    ]);
    $this->wp->addAction('login_init', [
      $this->wordpressMailerReplacer,
      'replaceWordPressMailer',
    ]);
    $this->wp->addAction('lostpassword_post', [
      $this->wordpressMailerReplacer,
      'replaceWordPressMailer',
    ]);
  }

  public function setupWooCommerceSubscriptionEvents() {
    $woocommerce = $this->settings->get('woocommerce', []);
    // WooCommerce: subscribe on checkout
    if (!empty($woocommerce['optin_on_checkout']['enabled'])) {
      $this->wp->addAction(
        'woocommerce_checkout_before_terms_and_conditions',
        [$this->hooksWooCommerce, 'extendWooCommerceCheckoutForm']
      );
    }

    $this->wp->addAction(
      'woocommerce_checkout_update_order_meta',
      [$this->hooksWooCommerce, 'subscribeOnCheckout'],
      10, // this should execute after the WC sync call on the same hook
      2
    );

    $this->wp->addAction(
      'woocommerce_before_pay_action',
      [$this->hooksWooCommerce, 'subscribeOnOrderPay'],
      10,
      1
    );
  }

  public function setupAutomateWooSubscriptionEvents() {
    $this->automateWooHooks->setup();
  }

  public function setupWPUsers() {
    // WP Users synchronization
    $this->wp->addAction(
      'user_register',
      [$this->wpSegment, 'synchronizeUser'],
      6
    );
    $this->wp->addAction(
      'added_existing_user',
      [$this->wpSegment, 'synchronizeUser'],
      6
    );
    $this->wp->addAction(
      'profile_update',
      [$this->wpSegment, 'synchronizeUser'],
      6,
      2
    );
    $this->wp->addAction(
      'add_user_role',
      [$this->wpSegment, 'synchronizeUser'],
      6,
      1
    );
    $this->wp->addAction(
      'set_user_role',
      [$this->wpSegment, 'synchronizeUser'],
      6,
      1
    );
    $this->wp->addAction(
      'delete_user',
      [$this->wpSegment, 'synchronizeUser'],
      1
    );
    // multisite
    $this->wp->addAction(
      'deleted_user',
      [$this->wpSegment, 'synchronizeUser'],
      1
    );
    $this->wp->addAction(
      'remove_user_from_blog',
      [$this->wpSegment, 'synchronizeUser'],
      1
    );

    // login
    $this->wp->addAction(
      'wp_login',
      [$this->subscriberHandler, 'identifyByLogin'],
      10,
      1
    );
  }

  public function setupWooCommerceSettings() {
    $this->wp->addAction('woocommerce_settings_start', [
      $this->hooksWooCommerce,
      'disableWooCommerceSettings',
    ]);

    $this->wp->addAction('before_woocommerce_init', [
      $this->hooksWooCommerce,
      'declareWooCompatibility',
    ]);

    $this->wp->addAction('init', [
      $this->hooksWooCommerce,
      'addMailPoetTaskToWooHomePage',
    ]);

    $this->wp->addFilter(
      'woocommerce_marketing_channels',
      [$this->hooksWooCommerce, 'addMailPoetMarketingMultiChannel'],
      10,
      1
    );
  }

  public function setupWoocommerceSystemInfo() {
    $this->wp->addAction(
      'woocommerce_system_status_report',
      [
        $this->wooSystemInfoController,
        'render',
      ]
    );
    $this->wp->addAction(
      'woocommerce_rest_prepare_system_status',
      [
        $this->wooSystemInfoController,
        'addFields',
      ]
    );
    $this->wp->addAction(
      'woocommerce_rest_system_status_schema',
      [
        $this->wooSystemInfoController,
        'addSchema',
      ]
    );
  }

  public function setupWooCommerceUsers() {
    // WooCommerce Customers synchronization
    $this->wp->addAction(
      'woocommerce_created_customer',
      [$this->hooksWooCommerce, 'synchronizeRegisteredCustomer'],
      7
    );
    $this->wp->addAction(
      'woocommerce_new_customer',
      [$this->hooksWooCommerce, 'synchronizeRegisteredCustomer'],
      7
    );
    $this->wp->addAction(
      'woocommerce_update_customer',
      [$this->hooksWooCommerce, 'synchronizeRegisteredCustomer'],
      7
    );
    $this->wp->addAction(
      'woocommerce_delete_customer',
      [$this->hooksWooCommerce, 'synchronizeRegisteredCustomer'],
      7
    );
    $this->wp->addAction(
      'woocommerce_checkout_update_order_meta',
      [$this->hooksWooCommerce, 'synchronizeGuestCustomer'],
      7
    );
    $this->wp->addAction(
      'woocommerce_process_shop_order_meta',
      [$this->hooksWooCommerce, 'synchronizeGuestCustomer'],
      7
    );
  }

  public function setupWooCommercePurchases() {
    $this->wp->addAction(
      'woocommerce_order_status_changed',
      [$this->hooksWooCommerce, 'trackPurchase'],
      10,
      1
    );

    $this->wp->addAction(
      'woocommerce_order_refunded',
      [$this->hooksWooCommerce, 'trackRefund'],
      10,
      1
    );
  }

  public function setupWooCommerceSubscriberEngagement() {
    $this->wp->addAction(
      'woocommerce_new_order',
      [$this->hooksWooCommerce, 'updateSubscriberEngagement'],
      7
    );
    // See class-wc-order.php, which says this about this action
    // "Fires when the order progresses from a pending payment status to a paid one"
    $this->wp->addAction(
      'woocommerce_order_payment_status_changed',
      [$this->hooksWooCommerce, 'updateSubscriberLastPurchase']
    );
  }

  public function setupWooCommerceTracking() {
    $this->wp->addFilter(
      'woocommerce_tracker_data',
      [$this->hooksWooCommerce, 'addTrackingData'],
      10
    );
  }

  public function setupListing() {
    $this->wp->addFilter(
      'set-screen-option',
      [$this, 'setScreenOption'],
      10,
      3
    );
  }

  public function setScreenOption($status, $option, $value) {
    if (preg_match('/^mailpoet_(.*)_per_page$/', $option)) {
      return $value;
    } else {
      return $status;
    }
  }

  public function setupPostNotifications() {
    $this->wp->addAction(
      'transition_post_status',
      [$this->postNotificationScheduler, 'transitionHook'],
      10,
      3
    );
  }

  public function setupFooter() {
    if (!Menu::isOnMailPoetAdminPage()) {
      return;
    }
    $this->wp->addFilter(
      'admin_footer_text',
      [$this, 'setFooter'],
      1,
      1
    );
  }

  public function setFooter(): string {

    if (Menu::isOnMailPoetAutomationPage()) {
      return '';
    }
    return '<a href="https://feedback.mailpoet.com/" rel="noopener noreferrer" target="_blank">' . esc_html__('Give feedback', 'mailpoet') . '</a>';
  }

  public function setupSettingsLinkInPluginPage() {
    $this->wp->addFilter(
      'plugin_action_links_' . Env::$pluginPath,
      [$this, 'setSettingsLinkInPluginPage']
    );
  }

  /**
   * @param array<string, string> $actionLinks
   * @return array<string, string>
   */
  public function setSettingsLinkInPluginPage(array $actionLinks): array {
    $customLinks = [
      'settings' => '<a href="' . $this->wp->adminUrl('admin.php?page=mailpoet-settings') . '" aria-label="' . $this->wp->escAttr(__('View MailPoet settings', 'mailpoet')) . '">' . $this->wp->escHtml(__('Settings', 'mailpoet')) . '</a>',
    ];

    return array_merge($customLinks, $actionLinks);
  }

  public function setupChangeNotifications(): void {
    $this->wp->addAction(
      'shutdown',
      [$this->subscriberChangesNotifier, 'notify']
    );
  }

  public function setupLicenseProvisioning(): void {
    $this->wp->addFilter(
      'wpcom_marketplace_webhook_response_mailpoet-business',
      [$this->dotcomLicenseProvisioner, 'provisionLicense'],
      10,
      3
    );
  }

  public function deactivateMailPoetCronBeforePluginUpgrade(): void {
    $this->wp->addFilter(
      'upgrader_pre_install',
      [$this, 'deactivateCronActions'],
      10,
      2
    );

    $this->wp->addAction(
      'action_scheduler_before_process_queue',
      [$this, 'deactivateCronWhenInMaintenanceMode']
    );
  }

  /**
   * Deactivates the MailPoet Cron actions.
   *
   * Hooked to the 'upgrader_pre_install' filter
   *
   * The cron will be reactivated automatically later in Initializer::initialize -> setupCronTrigger()
   *
   * @param bool|\WP_Error $response The installation response before the installation has started.
   * @param array         $plugin   Plugin package arguments.
   * @return bool|\WP_Error The original `$response` parameter or WP_Error.
   */
  public function deactivateCronActions($response, array $plugin) {
    if (is_wp_error($response)) { // skip
      return $response;
    }

    $pluginId = $plugin['plugin'] ?? '';

    if ($pluginId !== Env::$pluginPath) {
      // not updating MailPoet;
      return $response;
    }

    $this->cronTrigger->disable();

    return $response;
  }

  public function deactivateCronWhenInMaintenanceMode(): void {
    if (!$this->wp->wpIsMaintenanceMode()) {
      return;
    }

    $this->wp->addFilter('action_scheduler_queue_runner_batch_size', function () {
      // return 0 batch sizes to prevent the queue runner from running;
      // this is the fastest way to stop the current running cron
      return 0;
    });

    $this->cronTrigger->disable();
  }
}
