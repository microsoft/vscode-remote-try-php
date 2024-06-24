<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Listing;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Listing\ListingDefinition;
use MailPoet\Listing\ListingRepository;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

class NewsletterListingRepository extends ListingRepository {
  private static $supportedStatuses = [
    NewsletterEntity::STATUS_DRAFT,
    NewsletterEntity::STATUS_SCHEDULED,
    NewsletterEntity::STATUS_SENDING,
    NewsletterEntity::STATUS_SENT,
    NewsletterEntity::STATUS_ACTIVE,
  ];

  private static $supportedTypes = [
    NewsletterEntity::TYPE_STANDARD,
    NewsletterEntity::TYPE_RE_ENGAGEMENT,
    NewsletterEntity::TYPE_WELCOME,
    NewsletterEntity::TYPE_AUTOMATIC,
    NewsletterEntity::TYPE_NOTIFICATION,
    NewsletterEntity::TYPE_NOTIFICATION_HISTORY,
  ];

  public function getFilters(ListingDefinition $definition): array {
    $group = $definition->getGroup();
    $typeParam = $definition->getParameters()['type'] ?? null;
    $groupParam = $definition->getParameters()['group'] ?? null;

    // newsletter types without filters
    if (in_array($typeParam, [NewsletterEntity::TYPE_NOTIFICATION_HISTORY])) {
      return [];
    }

    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);

    if ($group) {
      $this->applyGroup($queryBuilder, $group);
    }

    if ($typeParam) {
      $this->applyType($queryBuilder, $typeParam, $groupParam);
    }

    $queryBuilder
      ->select('s.id, s.name, COUNT(n) AS newsletterCount')
      ->join('n.newsletterSegments', 'ns')
      ->join('ns.segment', 's')
      ->groupBy('s.id')
      ->addGroupBy('s.name')
      ->orderBy('s.name')
      ->having('COUNT(n) > 0');

    // format segment list
    $segmentList = [
      [
        'label' => __('All Lists', 'mailpoet'),
        'value' => '',
      ],
    ];

    foreach ($queryBuilder->getQuery()->getResult() as $item) {
      $segmentList[] = [
        'label' => sprintf('%s (%d)', $item['name'], $item['newsletterCount']),
        'value' => $item['id'],
      ];
    }
    return ['segment' => $segmentList];
  }

  public function getGroups(ListingDefinition $definition): array {
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);
    $this->applyParameters($queryBuilder, $definition->getParameters());

    // total count
    $countQueryBuilder = clone $queryBuilder;
    $countQueryBuilder->select('COUNT(n) AS newsletterCount');
    $countQueryBuilder->andWhere('n.deletedAt IS NULL');
    $totalCount = (int)$countQueryBuilder->getQuery()->getSingleScalarResult();

    // trashed count
    $trashedCountQueryBuilder = clone $queryBuilder;
    $trashedCountQueryBuilder->select('COUNT(n) AS newsletterCount');
    $trashedCountQueryBuilder->andWhere('n.deletedAt IS NOT NULL');
    $trashedCount = (int)$trashedCountQueryBuilder->getQuery()->getSingleScalarResult();

    // count-by-status query
    $queryBuilder->select('n.status, COUNT(n) AS newsletterCount');
    $queryBuilder->andWhere('n.deletedAt IS NULL');
    $queryBuilder->groupBy('n.status');

    $map = [];
    foreach ($queryBuilder->getQuery()->getResult() as $item) {
      $map[$item['status']] = (int)$item['newsletterCount'];
    }

    $groups = [
      [
        'name' => 'all',
        'label' => __('All', 'mailpoet'),
        'count' => $totalCount,
      ],
    ];

    $type = $definition->getParameters()['type'] ?? null;
    switch ($type) {
      case NewsletterEntity::TYPE_STANDARD:
        $groups = array_merge($groups, [
          [
            'name' => NewsletterEntity::STATUS_DRAFT,
            'label' => __('Draft', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_DRAFT] ?? 0,
          ],
          [
            'name' => NewsletterEntity::STATUS_SCHEDULED,
            'label' => __('Scheduled', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_SCHEDULED] ?? 0,
          ],
          [
            'name' => NewsletterEntity::STATUS_SENDING,
            'label' => __('Sending', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_SENDING] ?? 0,
          ],
          [
            'name' => NewsletterEntity::STATUS_SENT,
            'label' => __('Sent', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_SENT] ?? 0,
          ],
        ]);
        break;

      case NewsletterEntity::TYPE_NOTIFICATION_HISTORY:
        $groups = array_merge($groups, [
          [
            'name' => NewsletterEntity::STATUS_SENDING,
            'label' => __('Sending', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_SENDING] ?? 0,
          ],
          [
            'name' => NewsletterEntity::STATUS_SENT,
            'label' => __('Sent', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_SENT] ?? 0,
          ],
        ]);
        break;

      case NewsletterEntity::TYPE_WELCOME:
      case NewsletterEntity::TYPE_RE_ENGAGEMENT:
      case NewsletterEntity::TYPE_NOTIFICATION:
      case NewsletterEntity::TYPE_AUTOMATIC:
        $groups = array_merge($groups, [
          [
            'name' => NewsletterEntity::STATUS_ACTIVE,
            'label' => __('Active', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_ACTIVE] ?? 0,
          ],
          [
            'name' => NewsletterEntity::STATUS_DRAFT,
            'label' => __('Not active', 'mailpoet'),
            'count' => $map[NewsletterEntity::STATUS_DRAFT] ?? 0,
          ],
        ]);
        break;
    }

    $groups[] = [
      'name' => 'trash',
      'label' => __('Trash', 'mailpoet'),
      'count' => $trashedCount,
    ];

    return $groups;
  }

  protected function applySelectClause(QueryBuilder $queryBuilder) {
    $queryBuilder->select("PARTIAL n.{id,subject,hash,type,status,sentAt,updatedAt,deletedAt}, PARTIAL wpPost.{id,postTitle}");
  }

  protected function applyFromClause(QueryBuilder $queryBuilder) {
    $queryBuilder->from(NewsletterEntity::class, 'n')
      ->leftJoin('n.wpPost', 'wpPost');
  }

  protected function applyGroup(QueryBuilder $queryBuilder, string $group) {
    // include/exclude deleted
    if ($group === 'trash') {
      $queryBuilder->andWhere('n.deletedAt IS NOT NULL');
    } else {
      $queryBuilder->andWhere('n.deletedAt IS NULL');
    }

    if (!in_array($group, self::$supportedStatuses)) {
      return;
    }

    $queryBuilder
      ->andWhere('n.status = :status')
      ->setParameter('status', $group);
  }

  protected function applySearch(QueryBuilder $queryBuilder, string $search) {
    $search = Helpers::escapeSearch($search);
    $queryBuilder
      ->andWhere('n.subject LIKE :search')
      ->setParameter('search', "%$search%");
  }

  protected function applyFilters(QueryBuilder $queryBuilder, array $filters) {
    $segmentId = $filters['segment'] ?? null;
    if ($segmentId) {
      $queryBuilder
        ->join('n.newsletterSegments', 'ns')
        ->andWhere('ns.segment = :segmentId')
        ->setParameter('segmentId', $segmentId);
    }
  }

  protected function applyParameters(QueryBuilder $queryBuilder, array $parameters) {
    $type = $parameters['type'] ?? null;
    $group = $parameters['group'] ?? null;
    $parentId = $parameters['parentId'] ?? null;

    if ($type) {
      $this->applyType($queryBuilder, $type, $group);
    }

    if ($parentId) {
      $queryBuilder
        ->andWhere('n.parent = :parentId')
        ->setParameter('parentId', $parentId);
    }
  }

  protected function applySorting(QueryBuilder $queryBuilder, string $sortBy, string $sortOrder) {
    if ($sortBy === 'name') {
      $queryBuilder->addSelect('CONCAT(COALESCE(wpPost.postTitle, \'\'), n.subject) AS HIDDEN sortingName');
      $queryBuilder->addOrderBy("sortingName", $sortOrder);
      return;
    }
    if ($sortBy === 'sentAt') {
      $queryBuilder->addSelect('CASE WHEN n.sentAt IS NULL THEN 1 ELSE 0 END AS HIDDEN sentAtIsNull');
      $queryBuilder->addOrderBy('sentAtIsNull', 'DESC');
    }
    $queryBuilder->addOrderBy("n.$sortBy", $sortOrder);
  }

  private function applyType(QueryBuilder $queryBuilder, string $type, string $group = null) {
    if (!in_array($type, self::$supportedTypes)) {
      return;
    }

    if ($type === NewsletterEntity::TYPE_AUTOMATIC && $group) {
      $queryBuilder
        ->join('n.options', 'o')
        ->join('o.optionField', 'opf')
        ->andWhere('o.value = :group')
        ->setParameter('group', $group)
        ->andWhere('opf.newsletterType = n.type');
    } else {
      $queryBuilder
        ->andWhere('n.type = :type')
        ->setParameter('type', $type);
    }
  }
}
