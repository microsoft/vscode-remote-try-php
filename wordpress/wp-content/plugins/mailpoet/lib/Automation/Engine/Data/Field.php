<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;

class Field {
  public const TYPE_BOOLEAN = 'boolean';
  public const TYPE_INTEGER = 'integer';
  public const TYPE_NUMBER = 'number';
  public const TYPE_STRING = 'string';
  public const TYPE_ENUM = 'enum';
  public const TYPE_ENUM_ARRAY = 'enum_array';
  public const TYPE_DATETIME = 'datetime';

  /** @var string */
  private $key;

  /** @var string */
  private $type;

  /** @var string */
  private $name;

  /** @var callable */
  private $factory;

  /** @var array */
  private $args;

  public function __construct(
    string $key,
    string $type,
    string $name,
    callable $factory,
    array $args = []
  ) {

    $this->key = $key;
    $this->type = $type;
    $this->name = $name;
    $this->factory = $factory;
    $this->args = $args;
  }

  public function getKey(): string {
    return $this->key;
  }

  public function getType(): string {
    return $this->type;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getFactory(): callable {
    return $this->factory;
  }

  /** @return mixed */
  public function getValue(Payload $payload, array $params = []) {
    return $this->getFactory()($payload, $params);
  }

  public function getArgs(): array {
    return $this->args;
  }
}
