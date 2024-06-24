<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\ORM\EntityManager;

/**
 * @extends Repository<StatisticsWooCommercePurchaseEntity>
 */
class StatisticsWooCommercePurchasesRepository extends Repository {

  /** @var Helper */
  private $wooCommerceHelper;

  public function __construct(
    EntityManager $entityManager,
    Helper $wooCommerceHelper
  ) {
    parent::__construct($entityManager);
    $this->wooCommerceHelper = $wooCommerceHelper;
  }

  protected function getEntityClassName() {
    return StatisticsWooCommercePurchaseEntity::class;
  }

  public function createOrUpdateByClickDataAndOrder(StatisticsClickEntity $click, \WC_Order $order) {
    // search by subscriber and newsletter IDs (instead of click itself) to avoid duplicities
    // when a new click from the subscriber appeared since last tracking for given newsletter
    // (this will keep the originally tracked click - likely the click that led to the order)
    $statistics = $this->findOneBy([
      'orderId' => $order->get_id(),
      'subscriber' => $click->getSubscriber(),
      'newsletter' => $click->getNewsletter(),
    ]);

    if (!$statistics instanceof StatisticsWooCommercePurchaseEntity) {
      $newsletter = $click->getNewsletter();
      $queue = $click->getQueue();
      if ((!$newsletter instanceof NewsletterEntity) || (!$queue instanceof SendingQueueEntity)) return;
      $statistics = new StatisticsWooCommercePurchaseEntity(
        $newsletter,
        $queue,
        $click,
        $order->get_id(),
        $order->get_currency(),
        (float)$order->get_remaining_refund_amount(),
        $order->get_status()
      );
      $this->persist($statistics);
    } else {
      $statistics->setOrderCurrency($order->get_currency());
      $statistics->setOrderPriceTotal((float)$order->get_remaining_refund_amount());
      $statistics->setStatus($order->get_status());
    }
    $statistics->setSubscriber($click->getSubscriber());
    $this->flush();
  }

  public function getRevenuesByCampaigns(string $currency): array {
    $revenueStatus = $this->wooCommerceHelper->getPurchaseStates();
    $revenueStatsTable = $this->entityManager->getClassMetadata(StatisticsWooCommercePurchaseEntity::class)->getTableName();
    $newsletterTable = $this->entityManager->getClassMetadata(NewsletterEntity::class)->getTableName();

    // The "SELECT MIN(click_id)..." sub-query is used to count each purchase only once.
    // In the data we track a purchase to multiple newsletters if clicks from multiple newsletters occurred.
    /** @var array<int, array{revenue: float|int, campaign_id:int, orders_count:int}> $data */
    $data = $this->entityManager->getConnection()->executeQuery('
      SELECT
        SUM(swp.order_price_total) AS revenue,
        COALESCE(n.parent_id, swp.newsletter_id) AS campaign_id,
        (
            CASE
                WHEN n.type IS NULL THEN "unknown"
                WHEN n.type = :notification_history_type THEN :notification_type
                ELSE n.type
            END
          ) AS campaign_type,
        COUNT(order_id) as orders_count
      FROM ' . $revenueStatsTable . ' swp
      LEFT JOIN ' . $newsletterTable . ' n ON
          n.id = swp.newsletter_id
      WHERE
          swp.order_currency = :currency
          AND swp.status IN (:revenue_status)
          AND swp.click_id IN (SELECT MIN(click_id) FROM ' . $revenueStatsTable . ' ss GROUP BY order_id)
      GROUP BY campaign_id, n.type;
    ', [
      'notification_history_type' => NewsletterEntity::TYPE_NOTIFICATION_HISTORY,
      'notification_type' => NewsletterEntity::TYPE_NOTIFICATION,
      'currency' => $currency,
      'revenue_status' => $revenueStatus,
    ], [
      'notification_history_type' => ParameterType::STRING,
      'notification_type' => ParameterType::STRING,
      'currency' => ParameterType::STRING,
      'revenue_status' => Connection::PARAM_STR_ARRAY,
    ])->fetchAllAssociative();

    $data = array_map(function($row) {
      $row['revenue'] = round(floatval($row['revenue']), 2);
      $row['orders_count'] = intval($row['orders_count']);
      return $row;
    }, $data);
    return $data;
  }

  /** @param int[] $ids */
  public function removeNewsletterDataByNewsletterIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->update(StatisticsWooCommercePurchaseEntity::class, 'swp')
      ->set('swp.newsletter', ':newsletter')
      ->where('swp.newsletter IN (:ids)')
      ->setParameter('newsletter', null)
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // update was done via DQL, make sure the entities are also refreshed in the entity manager
    $this->refreshAll(function (StatisticsWooCommercePurchaseEntity $entity) use ($ids) {
      $newsletter = $entity->getNewsletter();
      return $newsletter && in_array($newsletter->getId(), $ids, true);
    });
  }
}
