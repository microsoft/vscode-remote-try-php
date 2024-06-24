<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Migrator\AppMigration;
use MailPoet\Segments\DynamicSegments\DynamicSegmentFilterRepository;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceAverageSpent;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceNumberOfOrders;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceSingleOrderValue;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceTotalSpent;

class Migration_20230712_180341 extends AppMigration {
  public function run(): void {
    $dynamicSegmentFilterRepository = $this->container->get(DynamicSegmentFilterRepository::class);
    $filters = $dynamicSegmentFilterRepository->findBy(
      [
        'filterData.action' => [
          WooCommerceNumberOfOrders::ACTION_NUMBER_OF_ORDERS,
          WooCommerceTotalSpent::ACTION_TOTAL_SPENT,
          WooCommerceSingleOrderValue::ACTION_SINGLE_ORDER_VALUE,
          WooCommerceAverageSpent::ACTION,
        ],
      ]
    );

    foreach ($filters as $filter) {
      $filterData = $filter->getFilterData();
      $data = $filter->getFilterData()->getData();

      if (isset($data['number_of_orders_days'])) {
        $days = $data['number_of_orders_days'];
      } else if (isset($data['total_spent_days'])) {
        $days = $data['total_spent_days'];
      } else if (isset($data['single_order_value_days'])) {
        $days = $data['single_order_value_days'];
      } else if (isset($data['average_spent_days'])) {
        $days = $data['average_spent_days'];
      }

      $filterType = $filterData->getFilterType();
      $filterAction = $filterData->getAction();

      if (isset($days) && is_string($filterType) && is_string($filterAction)) {
        $data['days'] = $days;
        $newFilterData = new DynamicSegmentFilterData($filterType, $filterAction, $data);
        $filter->setFilterData($newFilterData);
        $this->entityManager->persist($filter);
        $this->entityManager->flush();
      }
    }
  }
}
