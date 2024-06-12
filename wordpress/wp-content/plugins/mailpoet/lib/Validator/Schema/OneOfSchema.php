<?php declare(strict_types = 1);

namespace MailPoet\Validator\Schema;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema;

// See: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#oneof-and-anyof
class OneOfSchema extends Schema {
  protected $schema = [
    'oneOf' => [],
  ];

  /** @param Schema[] $schemas */
  public function __construct(
    array $schemas
  ) {
    foreach ($schemas as $schema) {
      $this->schema['oneOf'][] = $schema->toArray();
    }
  }

  public function nullable(): self {
    $null = ['type' => 'null'];
    $oneOf = $this->schema['oneOf'];
    $value = in_array($null, $oneOf, true) ? $oneOf : array_merge($oneOf, [$null]);
    return $this->updateSchemaProperty('oneOf', $value);
  }

  public function nonNullable(): self {
    $null = ['type' => 'null'];
    $oneOf = $this->schema['oneOf'];
    $value = array_filter($oneOf, function ($item) use ($null) {
      return $item !== $null;
    });
    return $this->updateSchemaProperty('oneOf', $value);
  }
}
