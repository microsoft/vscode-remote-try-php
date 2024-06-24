<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce\MultichannelMarketing;

if (!defined('ABSPATH')) exit;


use Automattic\WooCommerce\Admin\Marketing\MarketingCampaign;
use Automattic\WooCommerce\Admin\Marketing\MarketingCampaignType;
use Automattic\WooCommerce\Admin\Marketing\MarketingChannelInterface;
use Automattic\WooCommerce\Admin\Marketing\Price;
use MailPoet\Config\Menu;

class MPMarketingChannel implements MarketingChannelInterface {

  /**
   * @var MarketingCampaignType[]
   */
  private $campaignTypes;

  /**
   * @var MPMarketingChannelDataController
   */
  private $channelDataController;

  const CAMPAIGN_TYPE_NEWSLETTERS = 'mailpoet-newsletters';
  const CAMPAIGN_TYPE_POST_NOTIFICATIONS = 'mailpoet-post-notifications';
  const CAMPAIGN_TYPE_AUTOMATIONS = 'mailpoet-automations';

  public function __construct(
    MPMarketingChannelDataController $channelDataController
  ) {
    $this->channelDataController = $channelDataController;
    $this->campaignTypes = $this->generateCampaignTypes();
  }

  /**
   * Returns the unique identifier string for the marketing channel extension, also known as the plugin slug.
   *
   * @return string
   */
  public function get_slug(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return 'mailpoet';
  }

  /**
   * Returns the name of the marketing channel.
   *
   * @return string
   */
  public function get_name(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return __('MailPoet', 'mailpoet');
  }

  /**
   * Returns the description of the marketing channel.
   *
   * @return string
   */
  public function get_description(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return __('Create and send newsletters, post notifications and welcome emails from your WordPress.', 'mailpoet');
  }

  /**
   * Returns the path to the channel icon.
   *
   * @return string
   */
  public function get_icon_url(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return $this->channelDataController->getIconUrl();
  }

  /**
   * Returns the setup status of the marketing channel.
   *
   * @return bool
   */
  public function is_setup_completed(): bool { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return $this->channelDataController->isMPSetupComplete();
  }

  /**
   * Returns the URL to the settings page, or the link to complete the setup/onboarding if the channel has not been set up yet.
   *
   * @return string
   */
  public function get_setup_url(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    if ($this->channelDataController->isMPSetupComplete()) {
      return admin_url('admin.php?page=' . Menu::MAIN_PAGE_SLUG);
    }

    return admin_url('admin.php?page=' . Menu::WELCOME_WIZARD_PAGE_SLUG . '&mailpoet_wizard_loaded_via_woocommerce_marketing_dashboard');
  }

  /**
   * Returns the status of the marketing channel's product listings.
   *
   * @return string
   */
  public function get_product_listings_status(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    if (!$this->channelDataController->isMailPoetSendingServiceEnabled()) {
      return self::PRODUCT_LISTINGS_NOT_APPLICABLE;
    }

    // Check for error status. It's null by default when there isn't an error
    $sendingStatus = $this->channelDataController->getMailPoetSendingStatus();

    if ($sendingStatus) {
      return self::PRODUCT_LISTINGS_SYNC_FAILED;
    }

    return self::PRODUCT_LISTINGS_SYNCED;
  }

  /**
   * Returns the number of channel issues/errors (e.g. account-related errors, product synchronization issues, etc.).
   *
   * @return int The number of issues to resolve, or 0 if there are no issues with the channel.
   */
  public function get_errors_count(): int { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return $this->channelDataController->getErrorCount();
  }

  /**
   * Returns an array of marketing campaign types that the channel supports.
   *
   * @return MarketingCampaignType[] Array of marketing campaign type objects.
   */
  public function get_supported_campaign_types(): array { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return $this->campaignTypes;
  }

  /**
   * Returns an array of the channel's marketing campaigns.
   *
   * @return MarketingCampaign[]
   */
  public function get_campaigns(): array { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
      $allCampaigns = $this->generateCampaigns();

    if (empty($allCampaigns)) {
        return [];
    }

    return $allCampaigns;
  }

  /**
   * Generate the marketing channel campaign types
   *
   * @return MarketingCampaignType[]
   */
  protected function generateCampaignTypes(): array {
    return [
      self::CAMPAIGN_TYPE_NEWSLETTERS => new MarketingCampaignType(
        'mailpoet-newsletters',
        $this,
        __('MailPoet Newsletters', 'mailpoet'),
        __(
          'Send a newsletter with images, buttons, dividers, and social bookmarks. Or, just send a basic text email.',
          'mailpoet',
        ),
        admin_url('admin.php?page=' . Menu::EMAILS_PAGE_SLUG . '&loadedvia=woo_multichannel_dashboard#/new/standard'),
        $this->get_icon_url()
      ),
      self::CAMPAIGN_TYPE_POST_NOTIFICATIONS => new MarketingCampaignType(
        'mailpoet-post-notifications',
        $this,
        __('MailPoet Post Notifications', 'mailpoet'),
        __(
          'Let MailPoet email your subscribers with your latest content. You can send daily, weekly, monthly, or even immediately after publication.',
          'mailpoet',
        ),
        admin_url('admin.php?page=' . Menu::EMAILS_PAGE_SLUG . '&loadedvia=woo_multichannel_dashboard#/new/notification'),
        $this->get_icon_url()
      ),
      self::CAMPAIGN_TYPE_AUTOMATIONS => new MarketingCampaignType(
        'mailpoet-automations',
        $this,
        __('MailPoet Automations', 'mailpoet'),
        __('Set up automations to send abandoned cart reminders, welcome new subscribers, celebrate first-time buyers, and much more.', 'mailpoet'),
        admin_url('admin.php?page=' . Menu::AUTOMATION_TEMPLATES_PAGE_SLUG . '&loadedvia=woo_multichannel_dashboard'),
        $this->get_icon_url()
      ),
    ];
  }

  protected function generateCampaigns(): array {
      return array_map(
        function (array $data) {
            $cost = null;

          if (isset($data['price'])) {
              $cost = new Price((string)$data['price']['amount'], $data['price']['currency']);
          }

            return new MarketingCampaign(
              $data['id'],
              $data['campaignType'],
              $data['name'],
              $data['url'],
              $cost,
            );
        },
        array_merge(
          $this->channelDataController->getAutomations($this->campaignTypes[self::CAMPAIGN_TYPE_AUTOMATIONS]),
          $this->channelDataController->getPostNotificationNewsletters($this->campaignTypes[self::CAMPAIGN_TYPE_POST_NOTIFICATIONS]),
          $this->channelDataController->getStandardNewsletterList($this->campaignTypes[self::CAMPAIGN_TYPE_NEWSLETTERS])
        )
      );
  }
}
