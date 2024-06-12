<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class Step {
  public const TYPE_ROOT = 'root';
  public const TYPE_TRIGGER = 'trigger';
  public const TYPE_ACTION = 'action';

  /** @var string */
  private $id;

  /** @var string */
  private $type;

  /** @var string */
  private $key;

  /** @var array */
  protected $args;

  /** @var NextStep[] */
  protected $nextSteps;

  /** @var Filters|null */
  private $filters;

  /**
   * @param array<string, mixed> $args
   * @param NextStep[] $nextSteps
   */
  public function __construct(
    string $id,
    string $type,
    string $key,
    array $args,
    array $nextSteps,
    Filters $filters = null
  ) {
    $this->id = $id;
    $this->type = $type;
    $this->key = $key;
    $this->args = $args;
    $this->nextSteps = $nextSteps;
    $this->filters = $filters;
  }

  public function getId(): string {
    return $this->id;
  }

  public function getType(): string {
    return $this->type;
  }

  public function getKey(): string {
    return $this->key;
  }

  /** @return NextStep[] */
  public function getNextSteps(): array {
    return $this->nextSteps;
  }

  public function getNextStepIds(): array {
    $ids = [];
    foreach ($this->nextSteps as $nextStep) {
      $nextStepId = $nextStep->getId();
      if ($nextStepId) {
        $ids[] = $nextStep->getId();
      }
    }
    return $ids;
  }

  /** @param NextStep[] $nextSteps */
  public function setNextSteps(array $nextSteps): void {
    $this->nextSteps = $nextSteps;
  }

  public function getArgs(): array {
    return $this->args;
  }

  public function getFilters(): ?Filters {
    return $this->filters;
  }

  public function toArray(): array {
    return [
      'id' => $this->id,
      'type' => $this->type,
      'key' => $this->key,
      'args' => $this->args,
      'next_steps' => array_map(function (NextStep $nextStep) {
        return $nextStep->toArray();
      }, $this->nextSteps),
      'filters' => $this->filters ? $this->filters->toArray() : null,
    ];
  }

  public static function fromArray(array $data): self {
    return new self(
      $data['id'],
      $data['type'],
      $data['key'],
      $data['args'],
      array_map(function (array $nextStep) {
        return NextStep::fromArray($nextStep);
      }, $data['next_steps']),
      isset($data['filters']) ? Filters::fromArray($data['filters']) : null
    );
  }
}
