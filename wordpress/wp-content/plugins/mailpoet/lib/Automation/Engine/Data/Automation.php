<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Utils\Json;

class Automation {
  public const STATUS_ACTIVE = 'active';
  public const STATUS_DEACTIVATING = 'deactivating';
  public const STATUS_DRAFT = 'draft';
  public const STATUS_TRASH = 'trash';
  public const STATUS_ALL = [
    self::STATUS_ACTIVE,
    self::STATUS_DEACTIVATING,
    self::STATUS_DRAFT,
    self::STATUS_TRASH,
  ];

  /** @var int|null */
  private $id;

  /** @var int|null */
  private $versionId;

  /** @var string */
  private $name;

  /** @var \WP_User */
  private $author;

  /** @var string */
  private $status = self::STATUS_DRAFT;

  /** @var DateTimeImmutable */
  private $createdAt;

  /** @var DateTimeImmutable */
  private $updatedAt;

  /** @var ?DateTimeImmutable */
  private $activatedAt = null;

  /** @var array<string|int, Step> */
  private $steps;

  /** @var array<string, mixed> */
  private $meta = [];

  /** @param array<string, Step> $steps */
  public function __construct(
    string $name,
    array $steps,
    \WP_User $author,
    int $id = null,
    int $versionId = null
  ) {
    $this->name = $name;
    $this->steps = $steps;
    $this->author = $author;
    $this->id = $id;
    $this->versionId = $versionId;

    $now = new DateTimeImmutable();
    $this->createdAt = $now;
    $this->updatedAt = $now;
  }

  public function getId(): int {
    if ($this->id === null) {
      throw InvalidStateException::create()->withMessage('No automation ID was set');
    }
    return $this->id;
  }

  public function setId(int $id): void {
    $this->id = $id;
  }

  public function getVersionId(): int {
    if (!$this->versionId) {
      throw InvalidStateException::create()->withMessage('No automation version ID was set');
    }
    return $this->versionId;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): void {
    $this->name = $name;
    $this->setUpdatedAt();
  }

  public function getStatus(): string {
    return $this->status;
  }

  public function setStatus(string $status): void {
    if ($status === self::STATUS_ACTIVE && $this->status !== self::STATUS_ACTIVE) {
      $this->activatedAt = new DateTimeImmutable();
    }
    $this->status = $status;
    $this->setUpdatedAt();
  }

  public function getCreatedAt(): DateTimeImmutable {
    return $this->createdAt;
  }

  public function setCreatedAt(DateTimeImmutable $createdAt): void {
    $this->createdAt = $createdAt;
  }

  public function getAuthor(): \WP_User {
    return $this->author;
  }

  public function getUpdatedAt(): DateTimeImmutable {
    return $this->updatedAt;
  }

  public function getActivatedAt(): ?DateTimeImmutable {
    return $this->activatedAt;
  }

  /** @return array<string|int, Step> */
  public function getSteps(): array {
    return $this->steps;
  }

  /**
   * @return array<string|int, Step>
   */
  public function getTriggers(): array {
    return array_filter(
      $this->steps,
      function (Step $step) {
        return $step->getType() === Step::TYPE_TRIGGER;
      }
    );
  }

  /** @param array<string|int, Step> $steps */
  public function setSteps(array $steps): void {
    $this->steps = $steps;
    $this->setUpdatedAt();
  }

  public function getStep(string $id): ?Step {
    return $this->steps[$id] ?? null;
  }

  public function getTrigger(string $key): ?Step {
    foreach ($this->steps as $step) {
      if ($step->getType() === Step::TYPE_TRIGGER && $step->getKey() === $key) {
        return $step;
      }
    }
    return null;
  }

  public function equals(Automation $compare): bool {
    $compareArray = $compare->toArray();
    $currentArray = $this->toArray();
    $ignoreValues = [
      'created_at',
      'updated_at',
    ];
    foreach ($ignoreValues as $ignore) {
      unset($compareArray[$ignore]);
      unset($currentArray[$ignore]);
    }
    return $compareArray === $currentArray;
  }

  public function needsFullValidation(): bool {
    return in_array($this->status, [Automation::STATUS_ACTIVE, Automation::STATUS_DEACTIVATING], true);
  }

  public function toArray(): array {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'status' => $this->status,
      'author' => $this->author->ID,
      'created_at' => $this->createdAt->format(DateTimeImmutable::W3C),
      'updated_at' => $this->updatedAt->format(DateTimeImmutable::W3C),
      'activated_at' => $this->activatedAt ? $this->activatedAt->format(DateTimeImmutable::W3C) : null,
      'steps' => Json::encode(
        array_map(function (Step $step) {
          return $step->toArray();
        }, $this->steps)
      ),
      'meta' => Json::encode($this->meta),
    ];
  }

  private function setUpdatedAt(): void {
    $this->updatedAt = new DateTimeImmutable();
  }

  /**
   * @param string $key
   * @return mixed|null
   */
  public function getMeta(string $key) {
    return $this->meta[$key] ?? null;
  }

  public function getAllMetas(): array {
    return $this->meta;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function setMeta(string $key, $value): void {
    $this->meta[$key] = $value;
    $this->setUpdatedAt();
  }

  public function deleteMeta(string $key): void {
    unset($this->meta[$key]);
    $this->setUpdatedAt();
  }

  public function deleteAllMetas(): void {
    $this->meta = [];
    $this->setUpdatedAt();
  }

  public static function fromArray(array $data): self {
    // TODO: validation
    $automation = new self(
      $data['name'],
      array_map(function (array $stepData): Step {
        return Step::fromArray($stepData);
      }, Json::decode($data['steps'])),
      new \WP_User((int)$data['author'])
    );
    $automation->id = (int)$data['id'];
    $automation->versionId = (int)$data['version_id'];
    $automation->status = $data['status'];
    $automation->createdAt = new DateTimeImmutable($data['created_at']);
    $automation->updatedAt = new DateTimeImmutable($data['updated_at']);
    $automation->activatedAt = $data['activated_at'] !== null ? new DateTimeImmutable($data['activated_at']) : null;

    $automation->meta = $data['meta'] ? Json::decode($data['meta']) : [];
    return $automation;
  }
}
