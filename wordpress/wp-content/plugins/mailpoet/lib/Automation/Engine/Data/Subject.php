<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Utils\Json;

class Subject {
  /** @var string */
  private $key;

  /** @var array */
  private $args;

  public function __construct(
    string $key,
    array $args
  ) {
    $this->key = $key;
    $this->args = $args;
  }

  public function getKey(): string {
    return $this->key;
  }

  public function getArgs(): array {
    return $this->args;
  }

  public function getHash(): string {
    return md5($this->getKey() . serialize($this->getArgs()));
  }

  public function toArray(): array {
    return [
      'key' => $this->getKey(),
      'args' => Json::encode($this->getArgs()),
      'hash' => $this->getHash(),
    ];
  }

  public static function fromArray(array $data): self {
    return new self($data['key'], Json::decode($data['args']));
  }
}
