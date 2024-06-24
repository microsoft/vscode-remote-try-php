<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Migrator\AppMigration;
use MailPoet\Segments\DynamicSegments\DynamicSegmentFilterRepository;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceUsedPaymentMethod;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceUsedShippingMethod;

class Migration_20230803_200413_App extends AppMigration {
  public function run(): void {
    $dynamicSegmentFilterRepository = $this->container->get(DynamicSegmentFilterRepository::class);
    $filters = $dynamicSegmentFilterRepository->findBy(
      [
        'filterData.action' => [
          WooCommerceUsedPaymentMethod::ACTION,
          WooCommerceUsedShippingMethod::ACTION,
        ],
      ]
    );

    /** @var DynamicSegmentFilterEntity $filter */
    foreach ($filters as $filter) {
      $filterData = $filter->getFilterData();
      $data = $filter->getFilterData()->getData();

      if (isset($data['used_payment_method_days'])) {
        $days = $data['used_payment_method_days'];
      } elseif (isset($data['used_shipping_method_days'])) {
        $days = $data['used_shipping_method_days'];
      }

      $filterType = $filterData->getFilterType();
      $filterAction = $filterData->getAction();

      if (isset($days) && is_string($filterType) && is_string($filterAction)) {
        $data['days'] = $days;
        $data['timeframe'] = DynamicSegmentFilterData::TIMEFRAME_IN_THE_LAST;
        $newFilterData = new DynamicSegmentFilterData($filterType, $filterAction, $data);
        $filter->setFilterData($newFilterData);
        $this->entityManager->persist($filter);
        $this->entityManager->flush();
      }
    }
  }
}
