<?php declare(strict_types = 1);

namespace MailPoet\Validator;

if (!defined('ABSPATH')) exit;


use MailPoet\InvalidStateException;

use function json_encode;
use function rest_get_allowed_schema_keywords;

abstract class Schema {
  protected $schema = [];

  /** @return static */
  public function nullable() {
    $type = $this->schema['type'] ?? ['null'];
    return $this->updateSchemaProperty('type', is_array($type) ? $type : [$type, 'null']);
  }

  /** @return static */
  public function nonNullable() {
    $type = $this->schema['type'] ?? null;
    return $type === null
      ? $this->unsetSchemaProperty('type')
      : $this->updateSchemaProperty('type', is_array($type) ? $type[0] : $type);
  }

  /** @return static */
  public function required() {
    return $this->updateSchemaProperty('required', true);
  }

  /** @return static */
  public function optional() {
    return $this->unsetSchemaProperty('required');
  }

  /** @return static */
  public function title(string $title) {
    return $this->updateSchemaProperty('title', $title);
  }

  /** @return static */
  public function description(string $description) {
    return $this->updateSchemaProperty('description', $description);
  }

  /** @return static */
  public function default($default) {
    return $this->updateSchemaProperty('default', $default);
  }

  /** @return static */
  public function field(string $name, $value) {
    if (in_array($name, $this->getReservedKeywords(), true)) {
      throw new InvalidStateException("Field name '$name' is reserved");
    }
    return $this->updateSchemaProperty($name, $value);
  }

  public function toArray(): array {
    return $this->schema;
  }

  public function toString(): string {
    $json = json_encode($this->schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    $error = json_last_error();
    if ($error || $json === false) {
      throw new InvalidStateException(json_last_error_msg(), (string)$error);
    }
    return $json;
  }

  /** @return static */
  protected function updateSchemaProperty(string $name, $value) {
    $clone = clone $this;
    $clone->schema[$name] = $value;
    return $clone;
  }

  /** @return static */
  protected function unsetSchemaProperty(string $name) {
    $clone = clone $this;
    unset($clone->schema[$name]);
    return $clone;
  }

  protected function getReservedKeywords(): array {
    return rest_get_allowed_schema_keywords();
  }

  protected function validatePattern(string $pattern): void {
    $escaped = str_replace('#', '\\#', $pattern);
    $regex = "#$escaped#u";
    if (@preg_match($regex, '') === false) {
      throw new InvalidStateException("Invalid regular expression '$regex'");
    }
  }
}
