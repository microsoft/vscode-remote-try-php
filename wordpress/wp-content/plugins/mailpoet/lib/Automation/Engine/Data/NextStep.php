<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class NextStep {
  /** @var string|null */
  protected $id;

  public function __construct(
    ?string $id
  ) {
    $this->id = $id;
  }

  public function getId(): ?string {
    return $this->id;
  }

  public function toArray(): array {
    return [
      'id' => $this->id,
    ];
  }

  public static function fromArray(array $data): self {
    return new self($data['id']);
  }
}
