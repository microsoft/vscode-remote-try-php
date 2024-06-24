<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Analytics\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\Automation\Engine\Exceptions\UnexpectedValueException;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema;

class QueryWithCompare extends Query {

  /** @var \DateTimeImmutable */
  private $secondaryAfter;

  /** @var \DateTimeImmutable */
  private $secondaryBefore;

  public function __construct(
    \DateTimeImmutable $primaryAfter,
    \DateTimeImmutable $primaryBefore,
    \DateTimeImmutable $secondaryAfter,
    \DateTimeImmutable $secondaryBefore,
    int $limit = 25,
    string $orderBy = '',
    string $orderDirection = 'asc',
    int $page = 0,
    array $filter = [],
    string $search = null
  ) {
    parent::__construct($primaryAfter, $primaryBefore, $limit, $orderBy, $orderDirection, $page, $filter, $search);
    $this->secondaryAfter = $secondaryAfter;
    $this->secondaryBefore = $secondaryBefore;
  }

  public function getCompareWithAfter(): \DateTimeImmutable {
    return $this->secondaryAfter;
  }

  public function getCompareWithBefore(): \DateTimeImmutable {
    return $this->secondaryBefore;
  }

  /**
   * @param Request $request
   * @return QueryWithCompare
   * @throws UnexpectedValueException
   */
  public static function fromRequest(Request $request) {

    $query = $request->getParam('query');
    if (!is_array($query)) {
      throw new UnexpectedValueException('Invalid query parameters');
    }
    $primary = $query['primary'] ?? null;
    $secondary = $query['secondary'] ?? null;
    if (!is_array($primary) || !is_array($secondary)) {
      throw new UnexpectedValueException('Invalid query parameters');
    }
    $primaryAfter = $primary['after'] ?? null;
    $primaryBefore = $primary['before'] ?? null;
    $secondaryAfter = $secondary['after'] ?? null;
    $secondaryBefore = $secondary['before'] ?? null;
    if (
      !is_string($primaryAfter) ||
      !is_string($primaryBefore) ||
      !is_string($secondaryAfter) ||
      !is_string($secondaryBefore)
    ) {
      throw new UnexpectedValueException('Invalid query parameters');
    }

    $limit = $query['limit'] ?? 25;
    $orderBy = $query['orderBy'] ?? '';
    $orderDirection = $query['orderDirection'] ?? 'asc';
    $page = $query['page'] ?? 0;

    return new self(
      new \DateTimeImmutable($primaryAfter),
      new \DateTimeImmutable($primaryBefore),
      new \DateTimeImmutable($secondaryAfter),
      new \DateTimeImmutable($secondaryBefore),
      $limit,
      $orderBy,
      $orderDirection,
      $page
    );
  }

  public static function getRequestSchema(): Schema {
    return Builder::object(
      [
        'primary' => Builder::object(
          [
            'after' => Builder::string()->formatDateTime()->required(),
            'before' => Builder::string()->formatDateTime()->required(),
          ]
        ),
        'secondary' => Builder::object(
          [
            'after' => Builder::string()->formatDateTime()->required(),
            'before' => Builder::string()->formatDateTime()->required(),
          ]
        ),
        'limit' => Builder::integer()->minimum(1)->maximum(100),
        'orderBy' => Builder::string(),
        'orderDirection' => Builder::string(),
        'page' => Builder::integer()->minimum(1),
        'filter' => Builder::object(),
        'search' => Builder::string()->nullable(),
      ]
    );
  }
}
