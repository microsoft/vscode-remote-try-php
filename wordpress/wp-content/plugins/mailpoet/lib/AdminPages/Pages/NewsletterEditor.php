<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Form\Util\CustomFonts;
use MailPoet\Newsletter\Renderer\Blocks\Coupon;
use MailPoet\Newsletter\Shortcodes\ShortcodesHelper;
use MailPoet\NewsletterTemplates\BrandStyles;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\UserFlagsController;
use MailPoet\Subscribers\ConfirmationEmailCustomizer;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WooCommerce\TransactionalEmailHooks;
use MailPoet\WooCommerce\TransactionalEmails;
use MailPoet\WP\AutocompletePostListLoader as WPPostListLoader;
use MailPoet\WP\Functions as WPFunctions;

class NewsletterEditor {
  private const DATE_FORMAT = 'Y-m-d H:i:s';

  /** @var PageRenderer */
  private $pageRenderer;

  /** @var SettingsController */
  private $settings;

  /** @var UserFlagsController */
  private $userFlags;

  /** @var WooCommerceHelper */
  private $woocommerceHelper;

  /** @var WPFunctions */
  private $wp;

  /** @var TransactionalEmails */
  private $wcTransactionalEmails;

  /** @var ShortcodesHelper */
  private $shortcodesHelper;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var TransactionalEmailHooks */
  private $wooEmailHooks;

  /** @var WPPostListLoader */
  private $wpPostListLoader;

  /** @var CustomFonts  */
  private $customFonts;

  /*** @var AssetsController */
  private $assetsController;

  /** @var BrandStyles */
  private $brandStyles;

  public function __construct(
    PageRenderer $pageRenderer,
    SettingsController $settings,
    UserFlagsController $userFlags,
    WooCommerceHelper $woocommerceHelper,
    WPFunctions $wp,
    TransactionalEmails $wcTransactionalEmails,
    ShortcodesHelper $shortcodesHelper,
    SubscribersRepository $subscribersRepository,
    TransactionalEmailHooks $wooEmailHooks,
    WPPostListLoader $wpPostListLoader,
    CustomFonts $customFonts,
    AssetsController $assetsController,
    BrandStyles $brandStyles
  ) {
    $this->pageRenderer = $pageRenderer;
    $this->settings = $settings;
    $this->userFlags = $userFlags;
    $this->woocommerceHelper = $woocommerceHelper;
    $this->wp = $wp;
    $this->wcTransactionalEmails = $wcTransactionalEmails;
    $this->shortcodesHelper = $shortcodesHelper;
    $this->subscribersRepository = $subscribersRepository;
    $this->wooEmailHooks = $wooEmailHooks;
    $this->wpPostListLoader = $wpPostListLoader;
    $this->customFonts = $customFonts;
    $this->assetsController = $assetsController;
    $this->brandStyles = $brandStyles;
  }

  public function render() {
    $this->setupImageSize();
    $this->assetsController->setupNewsletterEditorDependencies();
    $newsletterId = (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    $woocommerceTemplateId = (int)$this->settings->get(TransactionalEmails::SETTING_EMAIL_ID, null);
    if (
      $woocommerceTemplateId
      && $newsletterId === $woocommerceTemplateId
      && !$this->woocommerceHelper->isWooCommerceActive()
    ) {
      $location = 'admin.php?page=mailpoet-settings&enable-customizer-notice#woocommerce';
      if (headers_sent()) {
        echo '<script>window.location = "' . esc_js($location) . '";</script>';
      } else {
        header('Location: ' . $location, true, 302);
      }
      exit;
    }

    $subscriber = $this->subscribersRepository->getCurrentWPUser();
    $subscriberData = $subscriber ? $this->formatSubscriber($subscriber) : [];
    $woocommerceData = [];
    if ($this->woocommerceHelper->isWooCommerceActive()) {
      // Activate hooks for Woo emails styles so that we always load styles set in Woo email customizer
      if ($newsletterId === (int)$this->settings->get(TransactionalEmails::SETTING_EMAIL_ID)) {
        $this->wooEmailHooks->overrideStylesForWooEmails();
      }
      $wcEmailSettings = $this->wcTransactionalEmails->getWCEmailSettings();
      $discountTypes = $this->woocommerceHelper->wcGetCouponTypes();
      $discountType = (string)current(array_keys($discountTypes));
      $amountMax = strpos($discountType, 'percent') !== false ? 100 : null;
      $woocommerceData = [
        'email_headings' => $this->wcTransactionalEmails->getEmailHeadings(),
        'customizer_enabled' => (bool)$this->settings->get('woocommerce.use_mailpoet_editor'),
        'coupon' => [
          'config' => [
            'discount_types' => array_map(function($label, $value): array {
              return ['label' => $label, 'value' => $value];
            }, $discountTypes, array_keys($discountTypes)),
            'code_placeholder' => Coupon::CODE_PLACEHOLDER,
            'price_decimal_separator' => $this->woocommerceHelper->wcGetPriceDecimalSeparator(),
          ],
          'defaults' => [
            'code' => Coupon::CODE_PLACEHOLDER,
            'discountType' => $discountType,
            'amountMax' => $amountMax,
          ],
        ],
      ];
      $woocommerceData = array_merge($wcEmailSettings, $woocommerceData);
    }

    $confirmationEmailTemplateId = (int)$this->settings->get(ConfirmationEmailCustomizer::SETTING_EMAIL_ID, null);

    $data = [
      'customFontsEnabled' => $this->customFonts->displayCustomFonts(),
      'shortcodes' => $this->shortcodesHelper->getShortcodes(),
      'settings' => $this->settings->getAll(),
      'editor_tutorial_seen' => $this->userFlags->get('editor_tutorial_seen'),
      'current_wp_user' => array_merge($subscriberData, $this->wp->wpGetCurrentUser()->to_array()),
      'woocommerce' => $woocommerceData,
      'is_wc_transactional_email' => $newsletterId === $woocommerceTemplateId,
      'is_confirmation_email_template' => $newsletterId === $confirmationEmailTemplateId,
      'is_confirmation_email_customizer_enabled' => (bool)$this->settings->get('signup_confirmation.use_mailpoet_editor', false),
      'product_categories' => $this->wpPostListLoader->getWooCommerceCategories(),
      'products' => $this->wpPostListLoader->getProducts(),
      'brand_styles' => [
        'available' => $this->brandStyles->isAvailable(),
      ],
    ];
    $this->wp->wpEnqueueMedia();
    $this->wp->wpEnqueueStyle('editor', $this->wp->includesUrl('css/editor.css'));
    $this->pageRenderer->displayPage('newsletter/editor.html', $data);
  }

  private function formatSubscriber(SubscriberEntity $subscriber): array {
    return [
      'id' => $subscriber->getId(),
      'wp_user_id' => $subscriber->getWpUserId(),
      'is_woocommerce_user' => (string)$subscriber->getIsWoocommerceUser(), // BC compatibility
      'first_name' => $subscriber->getFirstName(),
      'last_name' => $subscriber->getLastName(),
      'email' => $subscriber->getEmail(),
      'status' => $subscriber->getStatus(),
      'subscribed_ip' => $subscriber->getSubscribedIp(),
      'confirmed_ip' => $subscriber->getConfirmedIp(),
      'confirmed_at' => ($confirmedAt = $subscriber->getConfirmedAt()) ? $confirmedAt->format(self::DATE_FORMAT) : null,
      'last_subscribed_at' => ($lastSubscribedAt = $subscriber->getLastSubscribedAt()) ? $lastSubscribedAt->format(self::DATE_FORMAT) : null,
      'created_at' => ($createdAt = $subscriber->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
      'updated_at' => $subscriber->getUpdatedAt()->format(self::DATE_FORMAT),
      'deleted_at' => ($deletedAt = $subscriber->getDeletedAt()) ? $deletedAt->format(self::DATE_FORMAT) : null,
      'unconfirmed_data' => $subscriber->getUnconfirmedData(),
      'source' => $subscriber->getSource(),
      'count_confirmation' => $subscriber->getConfirmationsCount(),
      'unsubscribe_token' => $subscriber->getUnsubscribeToken(),
      'link_token' => $subscriber->getLinkToken(),
    ];
  }

  private function setupImageSize(): void {
    $this->wp->addFilter(
      'image_size_names_choose',
      function ($sizes): array {
        return array_merge($sizes, [
          'mailpoet_newsletter_max' => __('MailPoet Newsletter', 'mailpoet'),
        ]);
      }
    );
  }
}
