<?php declare(strict_types = 1);

namespace MailPoet\Listing;

if (!defined('ABSPATH')) exit;


class ListingDefinition {
  /** @var string|null */
  private $group;

  /** @var array */
  private $filters;

  /** @var string|null */
  private $search;

  /** @var array */
  private $parameters;

  /** @var string */
  private $sortBy;

  /** @var string */
  private $sortOrder;

  /** @var int */
  private $offset;

  /** @var int */
  private $limit;

  /** @var int[] */
  private $selection;

  public function __construct(
    string $group = null,
    array $filters = [],
    string $search = null,
    array $parameters = [],
    string $sortBy = 'created_at',
    string $sortOrder = 'desc',
    int $offset = 0,
    int $limit = 20,
    array $selection = []
  ) {
    $this->group = $group;
    $this->filters = $filters;
    $this->search = $search;
    $this->parameters = $parameters;
    $this->sortBy = $sortBy;
    $this->sortOrder = $sortOrder;
    $this->offset = $offset;
    $this->limit = $limit;
    $this->selection = array_map('intval', $selection);
  }

  /** @return string|null */
  public function getGroup() {
    return $this->group;
  }

  public function getFilters(): array {
    return $this->filters;
  }

  /** @return string|null */
  public function getSearch() {
    return $this->search;
  }

  public function getParameters(): array {
    return $this->parameters;
  }

  public function getSortBy(): string {
    return $this->sortBy;
  }

  public function getSortOrder(): string {
    return $this->sortOrder;
  }

  public function getOffset(): int {
    return $this->offset;
  }

  public function getLimit(): int {
    return $this->limit;
  }

  public function getSelection(): array {
    return $this->selection;
  }
}
