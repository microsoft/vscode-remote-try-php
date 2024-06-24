<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Statistics\StatisticsWooCommercePurchasesRepository;

class Tracker {

  /** @var StatisticsWooCommercePurchasesRepository */
  private $wooPurchasesRepository;

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var Helper */
  private $wooHelper;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  public function __construct(
    StatisticsWooCommercePurchasesRepository $wooPurchasesRepository,
    NewslettersRepository $newslettersRepository,
    Helper $wooHelper,
    LoggerFactory $loggerFactory
  ) {
    $this->wooPurchasesRepository = $wooPurchasesRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->wooHelper = $wooHelper;
    $this->loggerFactory = $loggerFactory;
  }

  public function addTrackingData(array $data): array {
    try {
      $currency = $this->wooHelper->getWoocommerceCurrency();
      $analyticsData = $this->newslettersRepository->getAnalytics();
      $data['extensions']['mailpoet'] = [
        'campaigns_count' => $analyticsData['campaigns_count'],
      ];
      $campaignData = $this->formatCampaignsData($this->wooPurchasesRepository->getRevenuesByCampaigns($currency));
      $data['extensions']['mailpoet'] = array_merge($data['extensions']['mailpoet'], $campaignData);
    } catch (\Throwable $e) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_TRACKING)->error($e->getMessage());
      return $data;
    }
    return $data;
  }

  /**
   * @param array<int, array{revenue: float, campaign_id: string|null, campaign_type: string, orders_count: int}> $campaignsData
   * @return array<string, string|int|float>
   */
  private function formatCampaignsData(array $campaignsData): array {
    return array_reduce($campaignsData, function($result, array $campaign): array {
      $newsletter = $this->newslettersRepository->findOneById((int)$campaign['campaign_id']);
      $keyPrefix = 'campaign_' . ($campaign['campaign_id'] ?? 0);
      $result[$keyPrefix . '_revenue'] = $campaign['revenue'];
      $result[$keyPrefix . '_orders_count'] = $campaign['orders_count'];
      $result[$keyPrefix . '_type'] = $campaign['campaign_type'];
      $result[$keyPrefix . '_event'] = $newsletter ? (string)$newsletter->getOptionValue(NewsletterOptionFieldEntity::NAME_EVENT) : '';
      return $result;
    }, []);
  }
}
