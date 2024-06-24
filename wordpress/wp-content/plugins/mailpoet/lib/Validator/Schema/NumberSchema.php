<?php declare(strict_types = 1);

namespace MailPoet\Validator\Schema;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema;

// See: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#numbers
class NumberSchema extends Schema {
  protected $schema = [
    'type' => 'number',
  ];

  public function minimum(float $value): self {
    return $this->updateSchemaProperty('minimum', $value)
      ->unsetSchemaProperty('exclusiveMinimum');
  }

  public function exclusiveMinimum(float $value): self {
    return $this->updateSchemaProperty('minimum', $value)
      ->updateSchemaProperty('exclusiveMinimum', true);
  }

  public function maximum(float $value): self {
    return $this->updateSchemaProperty('maximum', $value)
      ->unsetSchemaProperty('exclusiveMaximum');
  }

  public function exclusiveMaximum(float $value): self {
    return $this->updateSchemaProperty('maximum', $value)
      ->updateSchemaProperty('exclusiveMaximum', true);
  }

  public function multipleOf(float $value): self {
    return $this->updateSchemaProperty('multipleOf', $value);
  }
}
