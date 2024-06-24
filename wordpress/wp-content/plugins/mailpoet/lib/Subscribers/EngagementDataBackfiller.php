<?php declare(strict_types = 1);

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\DynamicSegments\Filters\FilterHelper;
use MailPoet\Segments\DynamicSegments\Filters\WooFilterHelper;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class EngagementDataBackfiller {
  /** @var EntityManager */
  private $entityManager;

  /** @var Helper */
  private $wcHelper;

  /** @var WooFilterHelper */
  private $wooFilterHelper;

  /** @var FilterHelper */
  private $filterHelper;

  /** @var int */
  private $lastProcessedSubscriberId = 0;

  public function __construct(
    EntityManager $entityManager,
    WooFilterHelper $wooFilterHelper,
    FilterHelper $filterHelper,
    Helper $wcHelper
  ) {
    $this->entityManager = $entityManager;
    $this->wcHelper = $wcHelper;
    $this->wooFilterHelper = $wooFilterHelper;
    $this->filterHelper = $filterHelper;
  }

  /**
   * @return SubscriberEntity[]
   */
  public function getBatch(int $lastProcessedId = 0, int $batchSize = 100): array {
    $subscribers = $this->entityManager->createQueryBuilder()
      ->select('PARTIAL s.{id, email, lastPurchaseAt, lastClickAt, lastOpenAt, lastSendingAt}')
      ->from(SubscriberEntity::class, 's')
      ->where('s.id > :lastProcessedId')
      ->orderBy('s.id', 'ASC')
      ->setMaxResults($batchSize)
      ->setParameter('lastProcessedId', $lastProcessedId)
      ->getQuery()
      ->getResult();
    if (!is_array($subscribers)) {
      return [];
    }
    return $subscribers;
  }

  /**
   * @param SubscriberEntity[] $subscribers
   *
   * @return void
   */
  public function updateBatch(array $subscribers): void {
    $subscriberIds = array_map(function (SubscriberEntity $subscriber) {
      return $subscriber->getId();
    }, $subscribers);

    $clickData = $this->getClickDataForBatch($subscriberIds);
    $openData = $this->getOpenDataForBatch($subscriberIds);
    $sendingData = $this->getSendingDataForBatch($subscriberIds);
    $purchaseData = $this->getPurchaseDataForBatch($subscriberIds);

    foreach ($subscribers as $subscriber) {
      if ($subscriber->getLastPurchaseAt() === null && isset($purchaseData[$subscriber->getId()]['last_purchase_at'])) {
        $purchaseDate = new Carbon($purchaseData[$subscriber->getId()]['last_purchase_at']);
        $subscriber->setLastPurchaseAt($purchaseDate);
      }
      if ($subscriber->getLastOpenAt() === null && isset($openData[$subscriber->getId()]['last_open_at'])) {
        $openDate = new Carbon($openData[$subscriber->getId()]['last_open_at']);
        $subscriber->setLastOpenAt($openDate);
      }
      if ($subscriber->getLastClickAt() === null && isset($clickData[$subscriber->getId()]['last_click_at'])) {
        $clickDate = new Carbon($clickData[$subscriber->getId()]['last_click_at']);
        $subscriber->setLastClickAt($clickDate);
      }
      if ($subscriber->getLastSendingAt() === null && isset($sendingData[$subscriber->getId()]['last_sending_at'])) {
        $sendingDate = new Carbon($sendingData[$subscriber->getId()]['last_sending_at']);
        $subscriber->setLastSendingAt($sendingDate);
      }
      if (is_int($subscriber->getId())) {
        $this->lastProcessedSubscriberId = $subscriber->getId();
      }
    }

    $this->entityManager->flush();
  }

  public function getClickDataForBatch(array $subscriberIds): array {
    $subscribersTable = $this->filterHelper->getSubscribersTable();
    $clicksTable = $this->filterHelper->getTableForEntity(StatisticsClickEntity::class);

    $query = $this
      ->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("$subscribersTable.id, MAX(clicks.created_at) as last_click_at")
      ->from($subscribersTable)
      ->innerJoin($subscribersTable, $clicksTable, 'clicks', "$subscribersTable.id = clicks.subscriber_id")
      ->andWhere("$subscribersTable.id IN (:subscriberIds)")
      ->setParameter('subscriberIds', $subscriberIds, Connection::PARAM_INT_ARRAY)
      ->groupBy("$subscribersTable.id");

    $result = $query->execute();
    if ($result instanceof Result) {
      return $result->fetchAllAssociativeIndexed();
    }
    return [];
  }

  public function getPurchaseDataForBatch(array $subscriberIds): array {
    if (!$this->wcHelper->isWooCommerceActive()) {
      return [];
    }

    $subscribersTable = $this->filterHelper->getSubscribersTable();

    $query = $this
      ->entityManager
      ->getConnection()
      ->createQueryBuilder()
      // The orderStats alias comes from wooFilterHelper->applyOrderStatusFilter, which calls wooFilterHelper->applyCustomerOrderJoin
      ->select("$subscribersTable.id, MAX(orderStats.date_created) as last_purchase_at")
      ->from($subscribersTable)
      ->andWhere("$subscribersTable.id IN (:subscriberIds)")
      ->setParameter('subscriberIds', $subscriberIds, Connection::PARAM_INT_ARRAY);
    $this->wooFilterHelper->applyOrderStatusFilter($query);
    $query->groupBy("$subscribersTable.id");

    $result = $query->execute();
    if ($result instanceof Result) {
      return $result->fetchAllAssociativeIndexed();
    }
    return [];
  }

  public function getOpenDataForBatch(array $subscriberIds): array {
    $subscribersTable = $this->filterHelper->getSubscribersTable();
    $opensTable = $this->filterHelper->getTableForEntity(StatisticsOpenEntity::class);

    $query = $this
      ->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("$subscribersTable.id, MAX(opens.created_at) as last_open_at")
      ->from($subscribersTable)
      ->innerJoin($subscribersTable, $opensTable, 'opens', "$subscribersTable.id = opens.subscriber_id")
      ->andWhere("$subscribersTable.id IN (:subscriberIds)")
      ->setParameter('subscriberIds', $subscriberIds, Connection::PARAM_INT_ARRAY)
      ->groupBy("$subscribersTable.id");

    $result = $query->execute();
    if ($result instanceof Result) {
      return $result->fetchAllAssociativeIndexed();
    }
    return [];
  }

  public function getSendingDataForBatch(array $subscriberIds): array {
    $subscribersTable = $this->filterHelper->getSubscribersTable();
    $sendsTable = $this->filterHelper->getTableForEntity(StatisticsNewsletterEntity::class);

    $query = $this
      ->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("$subscribersTable.id, MAX(sends.sent_at) as last_sending_at")
      ->from($subscribersTable)
      ->innerJoin($subscribersTable, $sendsTable, 'sends', "$subscribersTable.id = sends.subscriber_id")
      ->andWhere("$subscribersTable.id IN (:subscriberIds)")
      ->setParameter('subscriberIds', $subscriberIds, Connection::PARAM_INT_ARRAY)
      ->groupBy("$subscribersTable.id");

    $result = $query->execute();
    if ($result instanceof Result) {
      return $result->fetchAllAssociativeIndexed();
    }
    return [];
  }

  /**
   * @return int
   */
  public function getLastProcessedSubscriberId(): int {
    return $this->lastProcessedSubscriberId;
  }

  public function setLastProcessedSubscriberId(int $id): void {
    $this->lastProcessedSubscriberId = $id;
  }
}
