<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;
use MailPoet\Migrator\AppMigration;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Doctrine\DBAL\Connection;

class Migration_20240322_110443_App extends AppMigration {
  public function run(): void {
    $wooCommerceHelper = $this->container->get(Helper::class);

    // If Woo is not active and the table doesn't exist, we can skip this migration
    if (!$wooCommerceHelper->isWooCommerceActive()) {
      return;
    }

    $purchaseStatisticsTable = $this->getTableName(StatisticsWooCommercePurchaseEntity::class);
    $purchaseStatistics = $this->entityManager->getConnection()->fetchAllAssociative("
      SELECT order_id
      FROM {$purchaseStatisticsTable}
    ");

    global $wpdb;
    if ($wooCommerceHelper->isWooCommerceCustomOrdersTableEnabled()) {
      $ordersTable = $wooCommerceHelper->getOrdersTableName();
      $query = "
        SELECT id AS order_id, status AS status
        FROM `{$ordersTable}`
        WHERE type = 'shop_order' AND id in (:orderIds)
        ";
    } else {
      $query = "
        SELECT wpp.id AS order_id, wpp.post_status AS status
        FROM `{$wpdb->posts}` wpp
        WHERE wpp.post_type = 'shop_order'
        AND wpp.ID in (:orderIds)
      ";
    }

    foreach (array_chunk($purchaseStatistics, 2) as $chunk) {
      $orderIds = array_column($chunk, 'order_id');

      /** @var array{order_id: int, status: string}[] $orders */
      $orders = $this->entityManager->getConnection()->executeQuery(
        $query,
        ['orderIds' => $orderIds],
        ['orderIds' => Connection::PARAM_INT_ARRAY],
      )->fetchAllAssociative();

      foreach ($orders as $order) {
        $this->entityManager->getConnection()->executeStatement("
          UPDATE {$purchaseStatisticsTable}
          SET status = :status
          WHERE order_id = :orderId
        ", [
          'orderId' => $order['order_id'],
          'status' => str_replace('wc-', '', $order['status']), // WC order status in DB is prefixed with 'wc-'
        ]);
      }
    }
  }

  /**
   * @param class-string $entityClassName
   */
  private function getTableName(string $entityClassName): string {
    return $this->entityManager->getClassMetadata($entityClassName)->getTableName();
  }
}
