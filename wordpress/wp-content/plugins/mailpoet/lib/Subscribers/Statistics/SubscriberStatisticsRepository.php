<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\UserAgentEntity;
use MailPoet\Newsletter\Statistics\WooCommerceRevenue;
use MailPoet\WooCommerce\Helper as WCHelper;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

/**
 * @extends Repository<SubscriberEntity>
 */
class SubscriberStatisticsRepository extends Repository {

  /** @var WCHelper */
  private $wcHelper;

  public function __construct(
    EntityManager $entityManager,
    WCHelper $wcHelper
  ) {
    parent::__construct($entityManager);
    $this->wcHelper = $wcHelper;
  }

  protected function getEntityClassName() {
    return SubscriberEntity::class;
  }

  public function getStatistics(SubscriberEntity $subscriber, ?Carbon $startTime = null) {
    return new SubscriberStatistics(
      $this->getStatisticsClickCount($subscriber, $startTime),
      $this->getStatisticsOpenCount($subscriber, $startTime),
      $this->getStatisticsMachineOpenCount($subscriber, $startTime),
      $this->getTotalSentCount($subscriber, $startTime),
      $this->getWooCommerceRevenue($subscriber, $startTime)
    );
  }

  public function getStatisticsClickCount(SubscriberEntity $subscriber, ?Carbon $startTime = null): int {
    $queryBuilder = $this->getStatisticsCountQuery(StatisticsClickEntity::class, $subscriber);
    if ($startTime) {
      $this->applyDateConstraint($queryBuilder, $startTime);
    }
    return (int)$queryBuilder
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function getStatisticsOpenCountQuery(SubscriberEntity $subscriber, ?Carbon $startTime = null): QueryBuilder {
    $queryBuilder = $this->getStatisticsCountQuery(StatisticsOpenEntity::class, $subscriber);
    if ($startTime) {
      $this->applyDateConstraint($queryBuilder, $startTime);
    }
    return $queryBuilder;
  }

  public function getStatisticsOpenCount(SubscriberEntity $subscriber, ?Carbon $startTime = null): int {
    return (int)$this->getStatisticsOpenCountQuery($subscriber, $startTime)
      ->andWhere('(stats.userAgentType = :userAgentType)')
      ->setParameter('userAgentType', UserAgentEntity::USER_AGENT_TYPE_HUMAN)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function getStatisticsMachineOpenCount(SubscriberEntity $subscriber, ?Carbon $startTime = null): int {
    return (int)$this->getStatisticsOpenCountQuery($subscriber, $startTime)
      ->andWhere('(stats.userAgentType = :userAgentType)')
      ->setParameter('userAgentType', UserAgentEntity::USER_AGENT_TYPE_MACHINE)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function getTotalSentCount(SubscriberEntity $subscriber, ?Carbon $startTime = null): int {
    $queryBuilder = $this->getStatisticsCountQuery(StatisticsNewsletterEntity::class, $subscriber);
    if ($startTime) {
      $queryBuilder
        ->andWhere('stats.sentAt >= :dateTime')
        ->setParameter('dateTime', $startTime);
    }
    return (int)$queryBuilder
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function getStatisticsCountQuery(string $entityName, SubscriberEntity $subscriber): QueryBuilder {
    return $this->entityManager->createQueryBuilder()
      ->select('COUNT(DISTINCT stats.newsletter) as cnt')
      ->from($entityName, 'stats')
      ->where('stats.subscriber = :subscriber')
      ->setParameter('subscriber', $subscriber);
  }

  public function getWooCommerceRevenue(SubscriberEntity $subscriber, ?Carbon $startTime = null): ?WooCommerceRevenue {
    if (!$this->wcHelper->isWooCommerceActive()) {
      return null;
    }

    $revenueStatus = $this->wcHelper->getPurchaseStates();
    $currency = $this->wcHelper->getWoocommerceCurrency();
    $queryBuilder = $this->entityManager->createQueryBuilder()
      ->select('stats.orderPriceTotal')
      ->from(StatisticsWooCommercePurchaseEntity::class, 'stats')
      ->where('stats.subscriber = :subscriber')
      ->andWhere('stats.orderCurrency = :currency')
      ->setParameter('subscriber', $subscriber)
      ->setParameter('currency', $currency)
      ->andWhere('stats.status IN (:revenue_status)')
      ->setParameter('subscriber', $subscriber)
      ->setParameter('currency', $currency)
      ->setParameter('revenue_status', $revenueStatus)
      ->groupBy('stats.orderId, stats.orderPriceTotal');
    if ($startTime) {
      $queryBuilder
        ->andWhere('stats.createdAt >= :dateTime')
        ->setParameter('dateTime', $startTime);
    }
    $purchases =
      $queryBuilder->getQuery()
        ->getResult();
    $sum = array_sum(array_column($purchases, 'orderPriceTotal'));
    return new WooCommerceRevenue(
      $currency,
      (float)$sum,
      count($purchases),
      $this->wcHelper
    );
  }

  private function applyDateConstraint(QueryBuilder $queryBuilder, Carbon $startTime): QueryBuilder {
    $queryBuilder->join(StatisticsNewsletterEntity::class, 'sent_stats', 'WITH', 'stats.newsletter = sent_stats.newsletter AND stats.subscriber = sent_stats.subscriber AND sent_stats.sentAt >= :dateTime')
      ->setParameter('dateTime', $startTime);

    return $queryBuilder;
  }
}
