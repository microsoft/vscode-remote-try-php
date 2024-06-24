<?php declare(strict_types = 1);

namespace MailPoet\Validator\Schema;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema;

// See: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#arrays
class ArraySchema extends Schema {
  protected $schema = [
    'type' => 'array',
  ];

  public function items(Schema $schema): self {
    return $this->updateSchemaProperty('items', $schema->toArray());
  }

  public function minItems(int $value): self {
    return $this->updateSchemaProperty('minItems', $value);
  }

  public function maxItems(int $value): self {
    return $this->updateSchemaProperty('maxItems', $value);
  }

  public function uniqueItems(): self {
    return $this->updateSchemaProperty('uniqueItems', true);
  }
}
