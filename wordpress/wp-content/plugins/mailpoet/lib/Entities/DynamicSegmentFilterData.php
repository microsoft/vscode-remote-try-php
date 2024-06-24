<?php declare(strict_types = 1);

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\InvalidStateException;
use MailPoet\Segments\DynamicSegments\Filters\UserRole;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class DynamicSegmentFilterData {
  const TYPE_AUTOMATIONS = 'automations';
  const TYPE_USER_ROLE = 'userRole';
  const TYPE_EMAIL = 'email';
  const TYPE_WOOCOMMERCE = 'woocommerce';
  const TYPE_WOOCOMMERCE_MEMBERSHIP = 'woocommerceMembership';
  const TYPE_WOOCOMMERCE_SUBSCRIPTION = 'woocommerceSubscription';

  public const CONNECT_TYPE_AND = 'and';
  public const CONNECT_TYPE_OR = 'or';

  public const OPERATOR_ALL = 'all';
  public const OPERATOR_ANY = 'any';
  public const OPERATOR_NONE = 'none';

  public const OPERATOR_STARTS_WITH = 'startsWith';
  public const OPERATOR_NOT_ENDS_WITH = 'notEndsWith';
  public const OPERATOR_IS = 'is';
  public const OPERATOR_CONTAINS = 'contains';
  public const OPERATOR_NOT_CONTAINS = 'notContains';
  public const OPERATOR_NOT_STARTS_WITH = 'notStartsWith';
  public const OPERATOR_IS_NOT = 'isNot';
  public const OPERATOR_ENDS_WITH = 'endsWith';
  public const TEXT_FIELD_OPERATORS = [
    DynamicSegmentFilterData::OPERATOR_IS,
    DynamicSegmentFilterData::OPERATOR_IS_NOT,
    DynamicSegmentFilterData::OPERATOR_CONTAINS,
    DynamicSegmentFilterData::OPERATOR_NOT_CONTAINS,
    DynamicSegmentFilterData::OPERATOR_STARTS_WITH,
    DynamicSegmentFilterData::OPERATOR_NOT_STARTS_WITH,
    DynamicSegmentFilterData::OPERATOR_ENDS_WITH,
    DynamicSegmentFilterData::OPERATOR_NOT_ENDS_WITH,
  ];
  public const IS_NOT_BLANK = 'is_not_blank';
  public const IS_BLANK = 'is_blank';

  public const TIMEFRAME_ALL_TIME = 'allTime';
  public const TIMEFRAME_IN_THE_LAST = 'inTheLast';

  /**
   * @ORM\Column(type="serialized_array")
   * @var array|null
   */
  private $filterData;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $filterType;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $action;

  public function __construct(
    string $filterType,
    string $action,
    array $filterData = []
  ) {
    $this->filterType = $filterType;
    $this->action = $action;
    $this->filterData = $filterData;
  }

  public function getData(): ?array {
    return $this->filterData;
  }

  /**
   * @return mixed|null
   */
  public function getParam(string $name) {
    return $this->filterData[$name] ?? null;
  }

  public function getStringParam(string $name): string {
    $value = $this->filterData[$name] ?? null;
    if (!is_string($value)) {
      throw new InvalidStateException("No string value found in filter data for param $name.");
    }
    return $value;
  }

  public function getIntParam(string $name): int {
    $value = $this->filterData[$name] ?? null;
    if (is_int($value)) {
      return $value;
    }

    if (is_string($value)) {
      return (int)($value);
    }

    throw new InvalidStateException("No compatible integer value found in filter data for param $name.");
  }

  public function getArrayParam(string $name): array {
    $value = $this->getParam($name);
    if (!is_array($value)) {
      throw new InvalidStateException("No array value found in filter data for param $name.");
    }
    return $value;
  }

  public function getFilterType(): ?string {
    if ($this->filterType) {
      return $this->filterType;
    }
    // When a new column is empty, we try to get the value from serialized data
    return $this->filterData['segmentType'] ?? null;
  }

  public function getAction(): ?string {
    if ($this->action) {
      return $this->action;
    }
    // When a new column is empty, we try to get the value from serialized data
    // BC compatibility, the wordpress user role segment didn't have action
    if ($this->getFilterType() === self::TYPE_USER_ROLE && !isset($this->filterData['action'])) {
      return UserRole::TYPE;
    }
    return $this->filterData['action'] ?? null;
  }

  public function getOperator(): ?string {
    $operator = $this->filterData['operator'] ?? null;
    if (!$operator) {
      return $this->getDefaultOperator();
    }

    return $operator;
  }

  private function getDefaultOperator(): ?string {
    if ($this->getFilterType() === self::TYPE_WOOCOMMERCE && $this->getAction() === WooCommerceProduct::ACTION_PRODUCT) {
      return self::OPERATOR_ANY;
    }
    return null;
  }
}
