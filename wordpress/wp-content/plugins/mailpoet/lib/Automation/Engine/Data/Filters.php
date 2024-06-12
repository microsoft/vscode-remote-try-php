<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class Filters {
  public const OPERATOR_AND = 'and';
  public const OPERATOR_OR = 'or';

  /** @var string */
  private $operator;

  /** @var FilterGroup[] */
  private $groups;

  public function __construct(
    string $operator,
    array $groups
  ) {
    $this->operator = $operator;
    $this->groups = $groups;
  }

  public function getOperator(): string {
    return $this->operator;
  }

  public function getGroups(): array {
    return $this->groups;
  }

  public function toArray(): array {
    return [
      'operator' => $this->operator,
      'groups' => array_map(function (FilterGroup $group): array {
        return $group->toArray();
      }, $this->groups),
    ];
  }

  public static function fromArray(array $data): self {
    return new self(
      $data['operator'],
      array_map(function (array $group) {
        return FilterGroup::fromArray($group);
      }, $data['groups'])
    );
  }
}
