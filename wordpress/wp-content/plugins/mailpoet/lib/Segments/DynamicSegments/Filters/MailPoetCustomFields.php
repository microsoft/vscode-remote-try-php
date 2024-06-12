<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\Helpers;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class MailPoetCustomFields implements Filter {
  const TYPE = 'mailpoetCustomField';

  /** @var EntityManager */
  private $entityManager;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  public function __construct(
    EntityManager $entityManager,
    CustomFieldsRepository $customFieldsRepository
  ) {
    $this->entityManager = $entityManager;
    $this->customFieldsRepository = $customFieldsRepository;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $customFieldType = $filterData->getParam('custom_field_type');
    $customFieldId = $filterData->getParam('custom_field_id');
    $parameterSuffix = (string)($filter->getId() ?? Security::generateRandomString());
    $customFieldIdParam = ':customFieldId' . $parameterSuffix;

    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $subscribersCustomFieldTable = $this->entityManager->getClassMetadata(SubscriberCustomFieldEntity::class)->getTableName();

    $queryBuilder->leftJoin(
      $subscribersTable,
      $subscribersCustomFieldTable,
      'subscribers_custom_field',
      "$subscribersTable.id = subscribers_custom_field.subscriber_id AND subscribers_custom_field.custom_field_id = $customFieldIdParam"
    );
    $queryBuilder->setParameter($customFieldIdParam, $customFieldId);

    $valueParam = ':value' . $parameterSuffix;
    if (
      ($customFieldType === CustomFieldEntity::TYPE_TEXT)
      || ($customFieldType === CustomFieldEntity::TYPE_TEXTAREA)
      || ($customFieldType === CustomFieldEntity::TYPE_RADIO)
      || ($customFieldType === CustomFieldEntity::TYPE_SELECT)
    ) {
      $queryBuilder = $this->applyEquality($queryBuilder, $filter, $valueParam);
    }
    if ($customFieldType === CustomFieldEntity::TYPE_CHECKBOX) {
      $queryBuilder = $this->applyForCheckbox($queryBuilder, $filter);
    }
    if ($customFieldType === CustomFieldEntity::TYPE_DATE) {
      $queryBuilder = $this->applyForDate($queryBuilder, $filter, $valueParam);
    }
    return $queryBuilder;
  }

  private function applyForDate(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter, string $valueParam): QueryBuilder {
    $filterData = $filter->getFilterData();
    $dateType = $filterData->getParam('date_type');
    $value = $filterData->getParam('value');
    $operator = $filterData->getParam('operator');
    $queryBuilder->setParameter($valueParam, $value);
    if ($operator === DynamicSegmentFilterData::IS_BLANK) {
      $queryBuilder->andWhere('subscribers_custom_field.value IS NULL');
      return $queryBuilder;
    } elseif ($operator === DynamicSegmentFilterData::IS_NOT_BLANK) {
      $queryBuilder->andWhere('subscribers_custom_field.value IS NOT NULL');
      return $queryBuilder;
    } elseif ($dateType === 'month') {
      return $this->applyForDateMonth($queryBuilder, $valueParam);
    } elseif ($dateType === 'year') {
      return $this->applyForDateYear($queryBuilder, $operator, $valueParam);
    }
    return $this->applyForDateEqual($queryBuilder, $operator, $valueParam);
  }

  private function applyForDateMonth(QueryBuilder $queryBuilder, string $valueParam): QueryBuilder {
    $queryBuilder->andWhere("month(subscribers_custom_field.value) = month($valueParam)");
    return $queryBuilder;
  }

  private function applyForDateYear(QueryBuilder $queryBuilder, ?string $operator, string $valueParam): QueryBuilder {
    if ($operator === 'before') {
      $queryBuilder->andWhere("year(subscribers_custom_field.value) < year($valueParam)");
    } elseif ($operator === 'after') {
      $queryBuilder->andWhere("year(subscribers_custom_field.value) > year($valueParam)");
    } else {
      $queryBuilder->andWhere("year(subscribers_custom_field.value) = year($valueParam)");
    }
    return $queryBuilder;
  }

  private function applyForDateEqual(QueryBuilder $queryBuilder, ?string $operator, string $valueParam): QueryBuilder {
    if ($operator === 'before') {
      $queryBuilder->andWhere("subscribers_custom_field.value < $valueParam");
    } elseif ($operator === 'after') {
      $queryBuilder->andWhere("subscribers_custom_field.value > $valueParam");
    } else {
      // we always save full date in the database: 2018-03-01 00:00:00
      // so this works even for year_month where we save the first day of the month
      $queryBuilder->andWhere("subscribers_custom_field.value = $valueParam");
    }
    return $queryBuilder;
  }

  private function applyForCheckbox(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $value = $filterData->getParam('value');
    $operator = $filterData->getParam('operator');

    if ($operator === DynamicSegmentFilterData::IS_BLANK) {
      $queryBuilder->andWhere('subscribers_custom_field.value IS NULL');
    } elseif ($operator === DynamicSegmentFilterData::IS_NOT_BLANK) {
      $queryBuilder->andWhere('subscribers_custom_field.value IS NOT NULL');
    } elseif ($value === '1') {
      $queryBuilder->andWhere('subscribers_custom_field.value = 1');
    } elseif ($value === '0') {
      $queryBuilder->andWhere('subscribers_custom_field.value <> 1');
    }
    return $queryBuilder;
  }

  private function applyEquality(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter, string $valueParam): QueryBuilder {
    $filterData = $filter->getFilterData();

    $operator = $filterData->getParam('operator');
    $value = $filterData->getParam('value');

    $requiresValue = !in_array($operator, [DynamicSegmentFilterData::IS_BLANK, DynamicSegmentFilterData::IS_NOT_BLANK]);

    if ($requiresValue && !is_string($value)) {
      throw new InvalidFilterException('Missing required value', InvalidFilterException::MISSING_VALUE);
    }

    /** @var string $value - for PhpStan */

    if ($operator === 'equals') {
      $queryBuilder->andWhere("subscribers_custom_field.value = $valueParam");
      $queryBuilder->setParameter($valueParam, $value);
    } elseif ($operator === 'not_equals') {
      $queryBuilder->andWhere("subscribers_custom_field.value != $valueParam");
      $queryBuilder->orWhere('subscribers_custom_field.value IS NULL');
      $queryBuilder->setParameter($valueParam, $value);
    } elseif ($operator === 'more_than') {
      $queryBuilder->andWhere("subscribers_custom_field.value > $valueParam");
      $queryBuilder->setParameter($valueParam, $value);
    } elseif ($operator === 'less_than') {
      $queryBuilder->andWhere("subscribers_custom_field.value < $valueParam");
      $queryBuilder->setParameter($valueParam, $value);
    } elseif ($operator === DynamicSegmentFilterData::IS_BLANK) {
      $queryBuilder->andWhere('subscribers_custom_field.value IS NULL OR subscribers_custom_field.value = ""');
    } elseif ($operator === DynamicSegmentFilterData::IS_NOT_BLANK) {
      $queryBuilder->andWhere('subscribers_custom_field.value IS NOT NULL AND subscribers_custom_field.value != ""');
    } elseif ($operator === 'not_contains') {
      $queryBuilder->andWhere("subscribers_custom_field.value NOT LIKE $valueParam");
      $queryBuilder->setParameter($valueParam, '%' . Helpers::escapeSearch($value) . '%');
    } else {
      $queryBuilder->andWhere("subscribers_custom_field.value LIKE $valueParam");
      $queryBuilder->setParameter($valueParam, '%' . Helpers::escapeSearch($value) . '%');
    }

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $lookupData = [
      'customFields' => [],
    ];
    $customFieldId = $filterData->getIntParam('custom_field_id');
    $customField = $this->customFieldsRepository->findOneById($customFieldId);
    if ($customField instanceof CustomFieldEntity) {
      $lookupData['customFields'][$customFieldId] = $customField->getName();
    }
    return $lookupData;
  }
}
