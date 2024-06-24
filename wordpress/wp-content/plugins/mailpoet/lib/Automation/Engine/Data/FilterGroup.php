<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class FilterGroup {
  public const OPERATOR_AND = 'and';
  public const OPERATOR_OR = 'or';

  /** @var string */
  private $id;

  /** @var string */
  private $operator;

  /** @var Filter[] */
  private $filters;

  public function __construct(
    string $id,
    string $operator,
    array $filters
  ) {
    $this->id = $id;
    $this->operator = $operator;
    $this->filters = $filters;
  }

  public function getId(): string {
    return $this->id;
  }

  public function getOperator(): string {
    return $this->operator;
  }

  public function getFilters(): array {
    return $this->filters;
  }

  public function toArray(): array {
    return [
      'id' => $this->id,
      'operator' => $this->operator,
      'filters' => array_map(function (Filter $filter): array {
        return $filter->toArray();
      }, $this->filters),
    ];
  }

  public static function fromArray(array $data): self {
    return new self(
      $data['id'],
      $data['operator'],
      array_map(function (array $filter) {
        return Filter::fromArray($filter);
      }, $data['filters'])
    );
  }
}
