<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Twig;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Referrals\UrlDecorator;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\FreeDomains;
use MailPoet\Util\Notices\PendingApprovalNotice;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WPCOM\DotcomHelperFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Twig\Extension\AbstractExtension;
use MailPoetVendor\Twig\TwigFunction;

class Functions extends AbstractExtension {

  /** @var SettingsController */
  private $settings = null;

  /** @var WooCommerceHelper */
  private $woocommerceHelper = null;

  /** @var WPFunctions */
  private $wp = null;

  /** @var UrlDecorator */
  private $referralUrlDecorator = null;

  /** @var PendingApprovalNotice */
  private $pendingApprovalNotice = null;

  private function getWooCommerceHelper(): WooCommerceHelper {
    if ($this->woocommerceHelper === null) {
      $this->woocommerceHelper = new WooCommerceHelper($this->getWp());
    }
    return $this->woocommerceHelper;
  }

  private function getreferralUrlDecorator(): UrlDecorator {
    if ($this->referralUrlDecorator === null) {
      $this->referralUrlDecorator = new UrlDecorator($this->getWp(), $this->getSettings());
    }
    return $this->referralUrlDecorator;
  }

  private function getSettings(): SettingsController {
    if ($this->settings === null) {
      $this->settings = SettingsController::getInstance();
    }
    return $this->settings;
  }

  private function getDotcomHelperFunctions(): DotcomHelperFunctions {
    return ContainerWrapper::getInstance()->get(DotcomHelperFunctions::class);
  }

  private function getWp(): WPFunctions {
    if ($this->wp === null) {
      $this->wp = WPFunctions::get();
    }
    return $this->wp;
  }

  private function getPendingApprovalNotice(): PendingApprovalNotice {
    if ($this->pendingApprovalNotice === null) {
      $this->pendingApprovalNotice = ContainerWrapper::getInstance()->get(PendingApprovalNotice::class);
    }
    return $this->pendingApprovalNotice;
  }

  public function getFunctions() {
    return [
      new TwigFunction(
        'json_encode',
        'json_encode',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'json_decode',
        'json_decode',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'wp_nonce_field',
        'wp_nonce_field',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'params',
        [$this, 'params'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'admin_url',
        'admin_url',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'get_option',
        'get_option',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'sending_frequency',
        [$this, 'getSendingFrequency'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'wp_date_format',
        [$this, 'getWPDateFormat'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'mailpoet_version',
        [$this, 'getMailPoetVersion'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'mailpoet_premium_version',
        [$this, 'getMailPoetPremiumVersion'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'wp_date_format',
        [$this, 'getWPDateFormat'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'wp_time_format',
        [$this, 'getWPTimeFormat'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'wp_datetime_format',
        [$this, 'getWPDateTimeFormat'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'do_action',
        'do_action',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'is_rtl',
        [$this, 'isRtl'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'number_format_i18n',
        'number_format_i18n',
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'mailpoet_locale',
        [$this, 'getTwoLettersLocale'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'mailpoet_free_domains',
        [$this, 'getFreeDomains'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'is_woocommerce_active',
        [$this, 'isWoocommerceActive'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'get_woocommerce_version',
        [$this, 'getWooCommerceVersion'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'wp_start_of_week',
        [$this, 'getWPStartOfWeek'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'stats_color',
        [$this, 'statsColor'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'clicked_stats_text',
        [$this, 'clickedStatsText'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'stats_number_format_i18n',
        [$this, 'statsNumberFormatI18n'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'add_referral_id',
        [$this, 'addReferralId'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'is_loading_3rd_party_enabled',
        [$this, 'libs3rdPartyEnabled'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'is_dotcom_ecommerce_plan',
        [$this, 'isDotcomEcommercePlan'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'is_dotcom',
        [$this, 'isDotcom'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'pending_approval_message',
        [$this, 'pendingApprovalMessage'],
        ['is_safe' => ['html']]
      ),
    ];
  }

  public function getSendingFrequency() {
    /** @var string[] $args */
    $args = func_get_args();
    $value = (int)array_shift($args);

    $label = null;
    $labels = [
      'minute' => __('every minute', 'mailpoet'),
      // translators: %1$d is the amount of minutes.
      'minutes' => __('every %1$d minutes', 'mailpoet'),
      'hour' => __('every hour', 'mailpoet'),
      // translators: %1$d is the amount of hours.
      'hours' => __('every %1$d hours', 'mailpoet'),
    ];

    if ($value >= 60) {
      // we're dealing with hours
      if ($value === 60) {
        $label = $labels['hour'];
      } else {
        $label = $labels['hours'];
      }
      $value /= 60;
    } else {
      // we're dealing with minutes
      if ($value === 1) {
        $label = $labels['minute'];
      } else {
        $label = $labels['minutes'];
      }
    }

    return sprintf($label, $value);
  }

  public function getWPDateFormat() {
    return $this->getWp()->getOption('date_format') ?: 'F j, Y';
  }

  public function getWPStartOfWeek() {
    return $this->getWp()->getOption('start_of_week') ?: 0;
  }

  public function getMailPoetVersion() {
    return MAILPOET_VERSION;
  }

  public function getMailPoetPremiumVersion() {
    return (defined('MAILPOET_PREMIUM_VERSION')) ? MAILPOET_PREMIUM_VERSION : false;
  }

  public function getWPTimeFormat() {
    return $this->getWp()->getOption('time_format') ?: 'g:i a';
  }

  public function getWPDateTimeFormat() {
    return sprintf('%s %s', $this->getWPDateFormat(), $this->getWPTimeFormat());
  }

  public function params($key = null) {
    $args = $this->getWp()->stripslashesDeep($_GET);
    if (array_key_exists($key, $args)) {
      return $args[$key];
    }
    return null;
  }

  public function installedInLastTwoWeeks() {
    $maxNumberOfWeeks = 2;
    $installedAt = Carbon::createFromFormat('Y-m-d H:i:s', $this->getSettings()->get('installed_at'));
    if ($installedAt === false) {
      return false;
    }
    return $installedAt->diffInWeeks(Carbon::now()) < $maxNumberOfWeeks;
  }

  public function isRtl() {
    return $this->getWp()->isRtl();
  }

  public function getTwoLettersLocale() {
    return explode('_', $this->getWp()->getLocale())[0];
  }

  public function getFreeDomains() {
    return FreeDomains::FREE_DOMAINS;
  }

  public function isWoocommerceActive() {
    return $this->getWooCommerceHelper()->isWooCommerceActive();
  }

  public function getWooCommerceVersion() {
    return $this->getWooCommerceHelper()->getWooCommerceVersion();
  }

  public function statsColor($percentage) {
    if ($percentage > 3) {
      return '#7ed321';
    } elseif ($percentage > 1) {
      return '#ff9f00';
    } else {
      return '#f559c3';
    }
  }

  public function clickedStatsText($clicked) {
    if ($clicked > 3) {
      return __('Excellent', 'mailpoet');
    } elseif ($clicked > 1) {
      return __('Good', 'mailpoet');
    } else {
      return __('Average', 'mailpoet');
    }
  }

  /**
   * Wrapper around number_format_i18n() to return two decimals digits if the number
   * is smaller than 0.1 and one decimal digit if the number is equal or greater
   * than 0.1.
   *
   * @param int|float $number
   *
   * @return string
   */
  public function statsNumberFormatI18n($number) {
    if ($number < 0.1) {
      $decimals = 2;
    } else {
      $decimals = 1;
    }

    return number_format_i18n($number, $decimals);
  }

  public function addReferralId($url) {
    return $this->getreferralUrlDecorator()->decorate($url);
  }

  public function libs3rdPartyEnabled(): bool {
    return $this->getSettings()->get('3rd_party_libs.enabled') === '1';
  }

  public function isDotcomEcommercePlan(): bool {
    if (function_exists('wc_calypso_bridge_is_ecommerce_plan')) {
      return wc_calypso_bridge_is_ecommerce_plan();
    }
    return false;
  }

  public function isDotcom(): bool {
    return $this->getDotcomHelperFunctions()->isDotcom();
  }

  public function pendingApprovalMessage(): string {
    return $this->getPendingApprovalNotice()->getPendingApprovalMessage();
  }
}
