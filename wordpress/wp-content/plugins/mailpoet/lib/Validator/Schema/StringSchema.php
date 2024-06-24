<?php declare(strict_types = 1);

namespace MailPoet\Validator\Schema;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema;

// See: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#strings
class StringSchema extends Schema {
  protected $schema = [
    'type' => 'string',
  ];

  public function minLength(int $value): self {
    return $this->updateSchemaProperty('minLength', $value);
  }

  public function maxLength(int $value): self {
    return $this->updateSchemaProperty('maxLength', $value);
  }

  /**
   * Parameter $pattern is a regular expression without leading/trailing delimiters.
   * See: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#pattern
   */
  public function pattern(string $pattern): self {
    $this->validatePattern($pattern);
    return $this->updateSchemaProperty('pattern', $pattern);
  }

  public function formatDateTime(): self {
    return $this->updateSchemaProperty('format', 'date-time');
  }

  public function formatEmail(): self {
    return $this->updateSchemaProperty('format', 'email');
  }

  public function formatHexColor(): self {
    return $this->updateSchemaProperty('format', 'hex-color');
  }

  public function formatIp(): self {
    return $this->updateSchemaProperty('format', 'ip');
  }

  public function formatUri(): self {
    return $this->updateSchemaProperty('format', 'uri');
  }

  public function formatUuid(): self {
    return $this->updateSchemaProperty('format', 'uuid');
  }
}
