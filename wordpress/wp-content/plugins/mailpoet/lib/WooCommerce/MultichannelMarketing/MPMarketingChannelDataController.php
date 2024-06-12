<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce\MultichannelMarketing;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\OverviewStatisticsController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\QueryWithCompare;
use MailPoet\Config\Menu;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository;
use MailPoet\Newsletter\Statistics\WooCommerceRevenue;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\CdnAssetUrl;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Carbon\Carbon;

/**
 * Created to pass data to the `MPMarketingChannel`.
 *
 * We create an instance of this class with the MailPoet DI ContainerInterface
 *
 * This provides the class access to other MailPoet services,
 *  preventing us from overloading the `MPMarketingChannelController` class with imports and duplicating efforts
 */
class MPMarketingChannelDataController {

  /** @var CdnAssetUrl */
  private $cdnAssetUrl;

  /**
   * @var SettingsController
   */
  private $settings;

  /**
   * @var Bridge
   */
  private $bridge;

  /**
   * @var NewslettersRepository
   */
  private $newsletterRepository;

  /**
   * @var Helper
   */
  private $woocommerceHelper;

  /**
   * @var AutomationStorage
   */
  private $automationStorage;

  /**
   * @var NewsletterStatisticsRepository
   */
  private $newsletterStatisticsRepository;

  /**
   * @var OverviewStatisticsController
   */
  private $overviewStatisticsController;

  public function __construct(
    CdnAssetUrl $cdnAssetUrl,
    SettingsController $settings,
    Bridge $bridge,
    NewslettersRepository $newsletterRepository,
    Helper $woocommerceHelper,
    AutomationStorage $automationStorage,
    NewsletterStatisticsRepository $newsletterStatisticsRepository,
    OverviewStatisticsController $overviewStatisticsController
  ) {
    $this->cdnAssetUrl = $cdnAssetUrl;
    $this->settings = $settings;
    $this->bridge = $bridge;
    $this->newsletterRepository = $newsletterRepository;
    $this->automationStorage = $automationStorage;
    $this->woocommerceHelper = $woocommerceHelper;
    $this->newsletterStatisticsRepository = $newsletterStatisticsRepository;
    $this->overviewStatisticsController = $overviewStatisticsController;
  }

  public function getIconUrl(): string {
    return $this->cdnAssetUrl->generateCdnUrl('icon-white-123x128.png');
  }

  /**
   * Whether the task is completed.
   * If the setting 'version' is not null it means the welcome wizard
   * was already completed so we mark this task as completed as well.
   */
  public function isMPSetupComplete(): bool {
    $version = $this->settings->get('version');

    return $version !== null;
  }

  /**
   * Is MSS Enabled?
   * @return bool
   */
  public function isMailPoetSendingServiceEnabled(): bool {
    return $this->bridge->isMailpoetSendingServiceEnabled();
  }

  /**
   * Check for error status. It's null by default when there isn't an error
   * @return mixed
   */
  public function getMailPoetSendingStatus() {
    return $this->settings->get('mta_log.status');
  }

  /**
   * Get the number of errors available
   * Mostly likely sending errors
   * @return int
   */
  public function getErrorCount(): int {
    $error = $this->settings->get('mta_log.error');

    $count = 0;

    if (!empty($error)) {
      $count++;
    }

    $validationError = $this->settings->get(AuthorizedEmailsController::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING);

    if ($validationError && isset($validationError['invalid_sender_address'])) {
      $count++;
    }

    return $count;
  }

  public function getStandardNewsletterList($campaignType): array {
    return $this->getNewsletterTypeLists(
      // fetch the most recently sent post-notification history newsletters limited to ten
      $this->newsletterRepository->getStandardNewsletterListWithMultipleStatuses(10),
      $campaignType
    );
  }

  public function getPostNotificationNewsletters($campaignType): array {
    return $this->getNewsletterTypeLists(
      // fetch the most recently sent post-notification history newsletters limited to ten
      $this->newsletterRepository->getNotificationHistoryItems(10),
      $campaignType
    );
  }

  public function getAutomations($campaignType): array {
    $result = [];

    // Fetch Automation stats within the last 90 days
    $primaryAfter = new \DateTimeImmutable((string)Carbon::now()->subDays(90)->toISOString());
    $primaryBefore = new \DateTimeImmutable((string)Carbon::now()->toISOString());
    $now = new \DateTimeImmutable('');

    $query = new QueryWithCompare($primaryAfter, $primaryBefore, $now, $now);
    $userCurrency = $this->woocommerceHelper->getWoocommerceCurrency();

    foreach ($this->automationStorage->getAutomations([Automation::STATUS_ACTIVE]) as $automation) {
      $automationId = (string)$automation->getId();

      $automationStatistics = $this->overviewStatisticsController->getStatisticsForAutomation($automation, $query);

      $result[] = [
        'id' => $automationId,
        'name' => $automation->getName(),
        'campaignType' => $campaignType,
        'url' => admin_url('admin.php?page=' . Menu::AUTOMATION_ANALYTICS_PAGE_SLUG . '&id=' . $automationId),
        'price' => [
          'amount' => isset($automationStatistics['revenue']['current']) ? $this->formatPrice($automationStatistics['revenue']['current']) : 0,
          'currency' => $userCurrency,
        ],
      ];
    }

    return $result;
  }

  private function getNewsletterTypeLists($allNewsletters, $campaignType): array {
    $result = [];

    $userCurrency = $this->woocommerceHelper->getWoocommerceCurrency();

    // fetch the most recent newsletters limited to ten
    foreach ($allNewsletters as $newsletter) {
      $newsLetterId = (string)$newsletter->getId();

      /** @var ?WooCommerceRevenue $wooRevenue */
      $wooRevenue = $this->newsletterStatisticsRepository->getWooCommerceRevenue($newsletter);

      $result[] = [
        'id' => $newsLetterId,
        'name' => $newsletter->getSubject(),
        'campaignType' => $campaignType,
        'url' => admin_url('admin.php?page=' . Menu::EMAILS_PAGE_SLUG . '/#/stats/' . $newsLetterId),
        'price' => [
          'amount' => $wooRevenue ? $this->formatPrice($wooRevenue->getValue()) : 0,
          'currency' => $userCurrency,
        ],
      ];
    }

    return $result;
  }

  /**
   * Format amount to 2 dp
   * @param string|int|float $amount
   * @return string
   */
  private function formatPrice($amount): string {
    return number_format((float)$amount, 2, '.', '');
  }
}
