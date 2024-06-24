<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use InvalidArgumentException;
use MailPoet\Automation\Engine\Utils\Json;
use Throwable;

class AutomationRunLog {
  public const STATUS_RUNNING = 'running';
  public const STATUS_COMPLETE = 'complete';
  public const STATUS_FAILED = 'failed';

  public const STATUS_ALL = [
    self::STATUS_RUNNING,
    self::STATUS_COMPLETE,
    self::STATUS_FAILED,
  ];

  public const TYPE_ACTION = 'action';
  public const TYPE_TRIGGER = 'trigger';

  public const KEY_UNKNOWN = 'unknown';

  /** @var int */
  private $id;

  /** @var int */
  private $automationRunId;

  /** @var string */
  private $stepId;

  /** @var string */
  private $stepType;

  /** @var string */
  private $stepKey;

  /** @var string */
  private $status;

  /** @var DateTimeImmutable */
  private $startedAt;

  /** @var DateTimeImmutable */
  private $updatedAt;

  /** @var int */
  private $runNumber = 1;

  /** @var array */
  private $data = [];

  /** @var array|null */
  private $error;

  public function __construct(
    int $automationRunId,
    string $stepId,
    string $stepType,
    int $id = null
  ) {
    $this->automationRunId = $automationRunId;
    $this->stepId = $stepId;
    $this->stepType = $stepType;
    $this->stepKey = self::KEY_UNKNOWN;
    $this->status = self::STATUS_RUNNING;

    $now = new DateTimeImmutable();
    $this->startedAt = $now;
    $this->updatedAt = $now;

    if ($id) {
      $this->id = $id;
    }
  }

  public function getId(): int {
    return $this->id;
  }

  public function getAutomationRunId(): int {
    return $this->automationRunId;
  }

  public function getStepId(): string {
    return $this->stepId;
  }

  public function getStepType(): string {
    return $this->stepType;
  }

  public function getStepKey(): string {
    return $this->stepKey;
  }

  public function setStepKey(string $stepKey): void {
    $this->stepKey = $stepKey;
    $this->updatedAt = new DateTimeImmutable();
  }

  public function getStatus(): string {
    return $this->status;
  }

  public function setStatus(string $status): void {
    if (!in_array($status, self::STATUS_ALL, true)) {
      throw new InvalidArgumentException("Invalid status '$status'.");
    }
    $this->status = $status;
    $this->updatedAt = new DateTimeImmutable();
  }

  public function getStartedAt(): DateTimeImmutable {
    return $this->startedAt;
  }

  public function getUpdatedAt(): DateTimeImmutable {
    return $this->updatedAt;
  }

  public function getRunNumber(): int {
    return $this->runNumber;
  }

  public function setRunNumber(int $runNumber): void {
    $this->runNumber = $runNumber;
  }

  public function setUpdatedAt(DateTimeImmutable $updatedAt): void {
    $this->updatedAt = $updatedAt;
  }

  public function getData(): array {
    return $this->data;
  }

  /** @param mixed $value */
  public function setData(string $key, $value): void {
    if (!$this->isDataStorable($value)) {
      throw new InvalidArgumentException("Invalid data provided for key '$key'. Only scalar values and arrays of scalar values are allowed.");
    }
    $this->data[$key] = $value;
    $this->updatedAt = new DateTimeImmutable();
  }

  public function getError(): ?array {
    return $this->error;
  }

  public function toArray(): array {
    return [
      'id' => $this->id,
      'automation_run_id' => $this->automationRunId,
      'step_id' => $this->stepId,
      'step_type' => $this->stepType,
      'step_key' => $this->stepKey,
      'status' => $this->status,
      'started_at' => $this->startedAt->format(DateTimeImmutable::W3C),
      'updated_at' => $this->updatedAt->format(DateTimeImmutable::W3C),
      'run_number' => $this->runNumber,
      'data' => Json::encode($this->data),
      'error' => $this->error ? Json::encode($this->error) : null,
    ];
  }

  public function setError(Throwable $error): void {
    // Normalize all nested objects in error trace to associative arrays.
    // Empty objects would then get decoded to "[]" instead of "{}".
    $trace = Json::decode(Json::encode($error->getTrace()));
    $this->error = [
      'message' => $error->getMessage(),
      'errorClass' => get_class($error),
      'code' => $error->getCode(),
      'trace' => $trace,
    ];
    $this->updatedAt = new DateTimeImmutable();
  }

  public static function fromArray(array $data): self {
    $log = new AutomationRunLog((int)$data['automation_run_id'], $data['step_id'], $data['step_type']);
    $log->id = (int)$data['id'];
    $log->stepKey = $data['step_key'];
    $log->status = $data['status'];
    $log->startedAt = new DateTimeImmutable($data['started_at']);
    $log->updatedAt = new DateTimeImmutable($data['updated_at']);
    $log->runNumber = (int)$data['run_number'];
    $log->data = Json::decode($data['data']);
    $log->error = isset($data['error']) ? Json::decode($data['error']) : null;
    return $log;
  }

  /** @param mixed $data */
  private function isDataStorable($data): bool {
    if (is_scalar($data)) {
      return true;
    }

    if (!is_array($data)) {
      return false;
    }

    foreach ($data as $value) {
      if (!$this->isDataStorable($value)) {
        return false;
      }
    }
    return true;
  }
}
