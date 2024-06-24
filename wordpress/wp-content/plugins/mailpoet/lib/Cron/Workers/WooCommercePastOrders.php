<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Statistics\StatisticsClicksRepository;
use MailPoet\Statistics\Track\WooCommercePurchases;
use MailPoet\WooCommerce\Helper as WCHelper;
use MailPoetVendor\Carbon\Carbon;

class WooCommercePastOrders extends SimpleWorker {
  const TASK_TYPE = 'woocommerce_past_orders';
  const BATCH_SIZE = 20;

  /** @var WCHelper */
  private $woocommerceHelper;

  /** @var WooCommercePurchases */
  private $woocommercePurchases;

  /** @var StatisticsClicksRepository */
  private $statisticsClicksRepository;

  public function __construct(
    WCHelper $woocommerceHelper,
    StatisticsClicksRepository $statisticsClicksRepository,
    WooCommercePurchases $woocommercePurchases
  ) {
    $this->woocommerceHelper = $woocommerceHelper;
    $this->woocommercePurchases = $woocommercePurchases;
    $this->statisticsClicksRepository = $statisticsClicksRepository;
    parent::__construct();
  }

  public function checkProcessingRequirements() {
    return $this->woocommerceHelper->isWooCommerceActive() && empty($this->getCompletedTasks()); // run only once
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $oldestClick = $this->statisticsClicksRepository->findOneBy([], ['createdAt' => 'asc']);
    if (!$oldestClick instanceof StatisticsClickEntity) {
      return true;
    }

    // continue from 'last_processed_id' from previous run
    $meta = $task->getMeta();
    $lastId = isset($meta['last_processed_id']) ? $meta['last_processed_id'] : 0;
    add_filter('posts_where', function ($where = '') use ($lastId) {
      global $wpdb;
      return $where . " AND {$wpdb->prefix}posts.ID > " . $lastId;
    }, 10, 1);

    $orderIds = $this->woocommerceHelper->wcGetOrders([
      'date_completed' => '>=' . (($createdAt = $oldestClick->getCreatedAt()) ? $createdAt->format('Y-m-d H:i:s') : null),
      'orderby' => 'ID',
      'order' => 'ASC',
      'limit' => self::BATCH_SIZE,
      'return' => 'ids',
    ]);

    if (empty($orderIds)) {
      return true;
    }

    foreach ($orderIds as $orderId) {
      $this->woocommercePurchases->trackPurchase($orderId, false);
    }

    $task->setMeta(['last_processed_id' => end($orderIds)]);
    $this->scheduledTasksRepository->persist($task);
    $this->scheduledTasksRepository->flush();

    return false;
  }

  public function getNextRunDate() {
    return Carbon::createFromTimestamp($this->wp->currentTime('timestamp')); // schedule immediately
  }
}
