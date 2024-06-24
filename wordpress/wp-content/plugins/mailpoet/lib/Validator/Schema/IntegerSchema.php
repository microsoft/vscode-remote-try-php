<?php declare(strict_types = 1);

namespace MailPoet\Validator\Schema;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema;

// See: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#numbers
class IntegerSchema extends Schema {
  protected $schema = [
    'type' => 'integer',
  ];

  public function minimum(int $value): self {
    return $this->updateSchemaProperty('minimum', $value)
      ->unsetSchemaProperty('exclusiveMinimum');
  }

  public function exclusiveMinimum(int $value): self {
    return $this->updateSchemaProperty('minimum', $value)
      ->updateSchemaProperty('exclusiveMinimum', true);
  }

  public function maximum(int $value): self {
    return $this->updateSchemaProperty('maximum', $value)
      ->unsetSchemaProperty('exclusiveMaximum');
  }

  public function exclusiveMaximum(int $value): self {
    return $this->updateSchemaProperty('maximum', $value)
      ->updateSchemaProperty('exclusiveMaximum', true);
  }

  public function multipleOf(int $value): self {
    return $this->updateSchemaProperty('multipleOf', $value);
  }
}
