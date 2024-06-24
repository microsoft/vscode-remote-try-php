<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class EmailActionClickAny implements Filter {
  const TYPE = 'clickedAny';

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $newsletterLinksTable = $this->entityManager->getClassMetadata(NewsletterLinkEntity::class)->getTableName();

    $statsTable = $this->entityManager->getClassMetadata(StatisticsClickEntity::class)->getTableName();

    $excludedLinks = [
      '[link:subscription_unsubscribe_url]',
      '[link:subscription_instant_unsubscribe_url]',
      '[link:newsletter_view_in_browser_url]',
      '[link:subscription_manage_url]',
    ];
    $queryBuilder = $queryBuilder->innerJoin(
      $subscribersTable,
      $statsTable,
      'stats',
      "stats.subscriber_id = $subscribersTable.id"
    )->innerJoin(
      'stats',
      $newsletterLinksTable,
      'newsletterLinks',
      "stats.link_id = newsletterLinks.id AND newsletterLinks.URL NOT IN ('" . join("', '", $excludedLinks) . "')"
    );

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
