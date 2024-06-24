<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\UserAgentEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class EmailAction implements Filter {
  const ACTION_OPENED = 'opened';
  const ACTION_MACHINE_OPENED = 'machineOpened';
  /** @deprecated */
  const ACTION_NOT_OPENED = 'notOpened';
  const ACTION_CLICKED = 'clicked';
  const ACTION_WAS_SENT = 'wasSent';
  /** @deprecated */
  const ACTION_NOT_CLICKED = 'notClicked';

  const ALLOWED_ACTIONS = [
    self::ACTION_OPENED,
    self::ACTION_MACHINE_OPENED,
    self::ACTION_CLICKED,
    self::ACTION_WAS_SENT,
    EmailActionClickAny::TYPE,
    EmailOpensAbsoluteCountAction::TYPE,
    EmailOpensAbsoluteCountAction::MACHINE_TYPE,
    EmailsReceived::ACTION,
    NumberOfClicks::ACTION,
  ];

  /** @var EntityManager */
  private $entityManager;
  /** @var FilterHelper */
  private $filterHelper;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  public function __construct(
    EntityManager $entityManager,
    FilterHelper $filterHelper,
    NewslettersRepository $newslettersRepository,
    NewsletterLinkRepository $newsletterLinkRepository
  ) {
    $this->entityManager = $entityManager;
    $this->filterHelper = $filterHelper;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $action = $filterData->getAction();
    $parameterSuffix = (string)($filter->getId() ?? Security::generateRandomString());

    if ($action === self::ACTION_CLICKED) {
      return $this->applyForClickedActions($queryBuilder, $filterData, $parameterSuffix);
    } elseif ($action === self::ACTION_WAS_SENT) {
      return $this->applyForWasSentAction($queryBuilder, $filterData, $parameterSuffix);
    } else {
      return $this->applyForOpenedActions($queryBuilder, $filterData, $parameterSuffix);
    }
  }

  private function applyForClickedActions(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData, string $parameterSuffix): QueryBuilder {
    $operator = $filterData->getParam('operator') ?? DynamicSegmentFilterData::OPERATOR_ANY;
    $action = $filterData->getAction();
    $newsletterId = $filterData->getParam('newsletter_id');
    $linkIds = $filterData->getParam('link_ids');
    if (!is_array($linkIds)) {
      $linkIds = [];
    }

    $statsSentTable = $this->entityManager->getClassMetadata(StatisticsNewsletterEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $statsTable = $this->entityManager->getClassMetadata(StatisticsClickEntity::class)->getTableName();

    $where = '1';

    if (($action === self::ACTION_NOT_CLICKED) || ($operator === DynamicSegmentFilterData::OPERATOR_NONE)) {
      $queryBuilder = $queryBuilder->innerJoin(
        $subscribersTable,
        $statsSentTable,
        'statssent',
        "$subscribersTable.id = statssent.subscriber_id AND statssent.newsletter_id = :newsletter" . $parameterSuffix
      )->leftJoin(
        'statssent',
        $statsTable,
        'stats',
        $this->createNotStatsJoinCondition($parameterSuffix, $linkIds)
      )->setParameter('newsletter' . $parameterSuffix, $newsletterId);
      $where .= ' AND stats.id IS NULL';
    } else {
      $queryBuilder = $queryBuilder->innerJoin(
        $subscribersTable,
        $statsTable,
        'stats',
        "stats.subscriber_id = $subscribersTable.id AND stats.newsletter_id = :newsletter" . $parameterSuffix
      )->setParameter('newsletter' . $parameterSuffix, $newsletterId);
    }

    if ($action === EmailAction::ACTION_CLICKED && $operator !== DynamicSegmentFilterData::OPERATOR_NONE && $linkIds) {
      $where .= ' AND stats.link_id IN (:links' . $parameterSuffix . ')';
    }
    if ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
      $queryBuilder->groupBy('subscriber_id');
      if ($linkIds) {
        $queryBuilder->having('COUNT(1) = ' . count($linkIds));
      } else {
        // Case when a user selects all of, but doesn't specify links == all of all links.
        $linksTable = $this->entityManager->getClassMetadata(NewsletterLinkEntity::class)->getTableName();
        $linksQueryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $linkCount = $linksQueryBuilder->select('count(id)')
          ->from($linksTable)
          ->where('newsletter_id = :newsletter_id')
          ->setParameter('newsletter_id', $newsletterId)
          ->execute()
          ->fetchOne();
        $queryBuilder->having('COUNT(1) = ' . $linkCount);
      }
    }
    $queryBuilder = $queryBuilder->andWhere($where);
    if ($linkIds) {
      $queryBuilder = $queryBuilder
        ->setParameter('links' . $parameterSuffix, $linkIds, Connection::PARAM_STR_ARRAY);
    }
    return $queryBuilder;
  }

  private function applyForOpenedActions(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData, string $parameterSuffix): QueryBuilder {
    $operator = $filterData->getParam('operator') ?? DynamicSegmentFilterData::OPERATOR_ANY;
    $action = $filterData->getAction();
    $newsletters = $filterData->getParam('newsletters');

    $statsSentTable = $this->entityManager->getClassMetadata(StatisticsNewsletterEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $statsTable = $this->entityManager->getClassMetadata(StatisticsOpenEntity::class)->getTableName();

    $where = '1';

    if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $queryBuilder = $queryBuilder->innerJoin(
        $subscribersTable,
        $statsSentTable,
        'statssent',
        "$subscribersTable.id = statssent.subscriber_id AND statssent.newsletter_id IN (:newsletters" . $parameterSuffix . ')'
      )->leftJoin(
        'statssent',
        $statsTable,
        'stats',
        "statssent.subscriber_id = stats.subscriber_id AND stats.newsletter_id IN (:newsletters" . $parameterSuffix . ')'
      )->setParameter('newsletters' . $parameterSuffix, $newsletters, Connection::PARAM_INT_ARRAY);
      $where .= ' AND stats.id IS NULL';
    } else {
      $queryBuilder = $queryBuilder->innerJoin(
        $subscribersTable,
        $statsTable,
        'stats',
        "stats.subscriber_id = $subscribersTable.id AND stats.newsletter_id IN (:newsletters" . $parameterSuffix . ')'
      )->setParameter('newsletters' . $parameterSuffix, $newsletters, Connection::PARAM_INT_ARRAY);

      if ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
        $queryBuilder->groupBy('subscriber_id');
        $queryBuilder->having('COUNT(1) = ' . count($newsletters));
      }
    }
    if (($action === EmailAction::ACTION_OPENED) && ($operator !== DynamicSegmentFilterData::OPERATOR_NONE)) {
      $queryBuilder->andWhere('stats.user_agent_type = :userAgentType')
        ->setParameter('userAgentType', UserAgentEntity::USER_AGENT_TYPE_HUMAN);
    }
    if ($action === EmailAction::ACTION_MACHINE_OPENED) {
      $queryBuilder->andWhere('(stats.user_agent_type = :userAgentType)')
        ->setParameter('userAgentType', UserAgentEntity::USER_AGENT_TYPE_MACHINE);
    }
    $queryBuilder = $queryBuilder->andWhere($where);
    return $queryBuilder;
  }

  private function createNotStatsJoinCondition(string $parameterSuffix, array $linkIds = null): string {
    $clause = "statssent.subscriber_id = stats.subscriber_id AND stats.newsletter_id = :newsletter" . $parameterSuffix;
    if ($linkIds) {
      $clause .= ' AND stats.link_id IN (:links' . $parameterSuffix . ')';
    }
    return $clause;
  }

  private function applyForWasSentAction(QueryBuilder $queryBuilder, DynamicSegmentFilterData $filterData, string $parameterSuffix): QueryBuilder {
    $newsletters = (array)$filterData->getParam('newsletters');
    $operator = $filterData->getParam('operator') ?? DynamicSegmentFilterData::OPERATOR_ANY;
    $subscribersTable = $this->filterHelper->getSubscribersTable();
    $statisticsNewslettersTable = $this->entityManager->getClassMetadata(StatisticsNewsletterEntity::class)->getTableName();

    if ($operator === DynamicSegmentFilterData::OPERATOR_NONE) {
      $queryBuilder->leftJoin(
        $this->filterHelper->getSubscribersTable(),
        $statisticsNewslettersTable,
        'statisticsNewsletter',
        "$subscribersTable.id = statisticsNewsletter.subscriber_id AND statisticsNewsletter.newsletter_id IN (:newsletters" . $parameterSuffix . ')'
      )
        ->setParameter('newsletters' . $parameterSuffix, $newsletters, Connection::PARAM_INT_ARRAY)
        ->andWhere('statisticsNewsletter.subscriber_id IS NULL');
    } else {
      $queryBuilder->innerJoin(
        $subscribersTable,
        $statisticsNewslettersTable,
        'statisticsNewsletter',
        "statisticsNewsletter.subscriber_id = $subscribersTable.id AND statisticsNewsletter.newsletter_id IN (:newsletters" . $parameterSuffix . ')'
      )->setParameter('newsletters' . $parameterSuffix, $newsletters, Connection::PARAM_INT_ARRAY);

      if ($operator === DynamicSegmentFilterData::OPERATOR_ALL) {
        $queryBuilder->groupBy('subscriber_id');
        $queryBuilder->having('COUNT(1) = ' . count($newsletters));
      }
    }

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $lookupData = [
      'newsletters' => [],
      'links' => [],
    ];
    $newsletterIds = $filterData->getParam('newsletters');
    if (!is_array($newsletterIds)) {
      $newsletterIds = [];
    }

    // Clicked action only supports single newsletter ID
    $singularNewsletterId = $filterData->getParam('newsletter_id');
    if (!is_null($singularNewsletterId)) {
      $newsletterIds[] = $singularNewsletterId;
    }

    $linkIds = $filterData->getParam('link_ids');
    if (!is_array($linkIds)) {
      $linkIds = [];
    }

    foreach ($newsletterIds as $newsletterId) {
      $newsletter = $this->newslettersRepository->findOneById($newsletterId);
      if ($newsletter instanceof NewsletterEntity) {
        $lookupData['newsletters'][$newsletterId] = $newsletter->getSubject();
      }
    }

    foreach ($linkIds as $linkId) {
      $link = $this->newsletterLinkRepository->findOneById($linkId);
      if ($link instanceof NewsletterLinkEntity) {
        $lookupData['links'][$linkId] = $link->getUrl();
      }
    }

    return $lookupData;
  }
}
