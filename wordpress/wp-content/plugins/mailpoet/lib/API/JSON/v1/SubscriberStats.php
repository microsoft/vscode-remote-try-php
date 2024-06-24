<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Config\AccessControl;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\Statistics\SubscriberStatistics;
use MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Carbon\Carbon;

class SubscriberStats extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SUBSCRIBERS,
  ];

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SubscriberStatisticsRepository */
  private $subscribersStatisticsRepository;

  /** @var Helper */
  private $wooCommerceHelper;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    SubscriberStatisticsRepository $subscribersStatisticsRepository,
    Helper $wooCommerceHelper
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->subscribersStatisticsRepository = $subscribersStatisticsRepository;
    $this->wooCommerceHelper = $wooCommerceHelper;
  }

  public function get($data) {
    $subscriber = isset($data['subscriber_id'])
      ? $this->subscribersRepository->findOneById((int)$data['subscriber_id'])
      : null;
    if (!$subscriber instanceof SubscriberEntity) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This subscriber does not exist.', 'mailpoet'),
      ]);
    }
    $response = [
      'email' => $subscriber->getEmail(),
      'engagement_score' => $subscriber->getEngagementScore(),
      'is_woo_active' => $this->wooCommerceHelper->isWooCommerceActive(),
    ];

    $statsMapper = function(SubscriberStatistics $statistics, string $timeframe) {
      return [
        'timeframe' => $timeframe,
        'total_sent' => $statistics->getTotalSentCount(),
        'open' => $statistics->getOpenCount(),
        'machine_open' => $statistics->getMachineOpenCount(),
        'click' => $statistics->getClickCount(),
        'woocommerce' => $statistics->getWooCommerceRevenue() ? $statistics->getWooCommerceRevenue()->asArray() : null,
      ];
    };

    $lifetimeStats = $this->subscribersStatisticsRepository->getStatistics($subscriber);
    $oneYearStats = $this->subscribersStatisticsRepository->getStatistics($subscriber, Carbon::now()->subYear());
    $thirtyDaysStats = $this->subscribersStatisticsRepository->getStatistics($subscriber, Carbon::now()->subDays(30));

    $response['periodic_stats'] = [
      // translators: table header meaning 30 days
      $statsMapper($thirtyDaysStats, __('30(d)', 'mailpoet')),
      // translators: table header meaning 12 months
      $statsMapper($oneYearStats, __('12(m)', 'mailpoet')),
      $statsMapper($lifetimeStats, __('Lifetime', 'mailpoet')),
    ];

    $dateFormat = 'Y-m-d H:i:s';
    $lastEngagement = $subscriber->getLastEngagementAt();
    if ($lastEngagement instanceof \DateTimeInterface) {
      $response['last_engagement'] = $lastEngagement->format($dateFormat);
    }
    $lastClick = $subscriber->getLastClickAt();
    if ($lastClick instanceof \DateTimeInterface) {
      $response['last_click'] = $lastClick->format($dateFormat);
    }
    $lastOpen = $subscriber->getLastOpenAt();
    if ($lastOpen instanceof \DateTimeInterface) {
      $response['last_open'] = $lastOpen->format($dateFormat);
    }
    $lastPageView = $subscriber->getLastPageViewAt();
    if ($lastPageView instanceof \DateTimeInterface) {
      $response['last_page_view'] = $lastPageView->format($dateFormat);
    }
    $lastPurchase = $subscriber->getLastPurchaseAt();
    if ($lastPurchase instanceof \DateTimeInterface) {
      $response['last_purchase'] = $lastPurchase->format($dateFormat);
    }
    $lastSending = $subscriber->getLastSendingAt();
    if ($lastSending instanceof \DateTimeInterface) {
      $response['last_sending'] = $lastSending->format($dateFormat);
    }
    return $this->successResponse($response);
  }
}
