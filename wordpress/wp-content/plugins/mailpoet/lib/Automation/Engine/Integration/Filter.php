<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Integration;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Validator\Schema\ObjectSchema;

interface Filter {
  public function getFieldType(): string;

  /** @return array<string, string> */
  public function getConditions(): array;

  public function getArgsSchema(string $condition): ObjectSchema;

  public function getFieldParams(FilterData $data): array;

  /** @param mixed $value */
  public function matches(FilterData $data, $value): bool;
}
