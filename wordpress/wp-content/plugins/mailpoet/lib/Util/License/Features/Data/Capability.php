<?php declare(strict_types = 1);

namespace MailPoet\Util\License\Features\Data;

if (!defined('ABSPATH')) exit;


class Capability {
  public const TYPE_BOOLEAN = 'boolean';
  public const TYPE_NUMBER = 'number';
  public string $name;
  public string $type;
  public ?int $value;
  public bool $isRestricted;

  public function __construct(
    string $name,
    string $type = self::TYPE_BOOLEAN,
    bool $isRestricted = false,
    ?int $value = null
  ) {
    $this->name = $name;
    $this->type = $type;
    $this->value = ($type === self::TYPE_NUMBER) ? $value : null;
    $this->isRestricted = $isRestricted;
  }
}
