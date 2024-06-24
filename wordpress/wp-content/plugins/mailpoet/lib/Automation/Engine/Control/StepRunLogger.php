<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Storage\AutomationRunLogStorage;
use MailPoet\InvalidStateException;
use Throwable;

class StepRunLogger {
  /** @var AutomationRunLogStorage */
  private $automationRunLogStorage;

  /** @var Hooks */
  private $hooks;

  /** @var int */
  private $runId;

  /** @var string */
  private $stepId;

  /** @var AutomationRunLog|null */
  private $log;

  /** @var string */
  private $stepType;

  /** @var int */
  private $runNumber;

  /** @var bool */
  private $isWpDebug;

  public function __construct(
    AutomationRunLogStorage $automationRunLogStorage,
    Hooks $hooks,
    int $runId,
    string $stepId,
    string $stepType,
    int $runNumber,
    bool $isWpDebug = null
  ) {
    $this->automationRunLogStorage = $automationRunLogStorage;
    $this->hooks = $hooks;
    $this->runId = $runId;
    $this->stepId = $stepId;
    $this->stepType = $stepType;
    $this->runNumber = $runNumber;
    $this->isWpDebug = $isWpDebug !== null ? $isWpDebug : $this->getWpDebug();
  }

  private function getWpDebug(): bool {
    if (!defined('WP_DEBUG')) {
      return false;
    }
    if (!is_bool(WP_DEBUG)) {
      return in_array(strtolower((string)WP_DEBUG), ['true', '1'], true);
    }
    return WP_DEBUG;
  }

  public function logStart(): void {
    $log = $this->getLog();
    $log->setStatus(AutomationRunLog::STATUS_RUNNING);
    $log->setUpdatedAt(new DateTimeImmutable());
    $this->automationRunLogStorage->updateAutomationRunLog($log);
  }

  public function logStepData(Step $step): void {
    $log = $this->getLog();
    $log->setStepKey($step->getKey());
    $this->automationRunLogStorage->updateAutomationRunLog($log);
  }

  public function logProgress(): void {
    $log = $this->getLog();
    $log->setStatus(AutomationRunLog::STATUS_RUNNING);
    $log->setUpdatedAt(new DateTimeImmutable());
    $this->automationRunLogStorage->updateAutomationRunLog($log);
  }

  public function logSuccess(): void {
    $log = $this->getLog();
    $log->setStatus(AutomationRunLog::STATUS_COMPLETE);
    $log->setUpdatedAt(new DateTimeImmutable());
    $this->triggerAfterRunHook($log);
    $this->automationRunLogStorage->updateAutomationRunLog($log);
  }

  public function logFailure(Throwable $error): void {
    $log = $this->getLog();
    $log->setStatus(AutomationRunLog::STATUS_FAILED);
    $log->setError($error);
    $log->setUpdatedAt(new DateTimeImmutable());
    $this->triggerAfterRunHook($log);
    $this->automationRunLogStorage->updateAutomationRunLog($log);
  }

  private function getLog(): AutomationRunLog {
    if (!$this->log) {
      $this->log = $this->automationRunLogStorage->getAutomationRunLogByRunAndStepId($this->runId, $this->stepId);
    }

    if (!$this->log) {
      $log = new AutomationRunLog($this->runId, $this->stepId, $this->stepType);
      $log->setRunNumber($this->runNumber);
      $id = $this->automationRunLogStorage->createAutomationRunLog($log);
      $this->log = $this->automationRunLogStorage->getAutomationRunLog($id);
    }

    if (!$this->log) {
      throw new InvalidStateException('Failed to create automation run log');
    }

    $this->log->setRunNumber($this->runNumber);
    return $this->log;
  }

  private function triggerAfterRunHook(AutomationRunLog $log): void {
    try {
      $this->hooks->doAutomationStepAfterRun($log);
    } catch (Throwable $e) {
      if ($this->isWpDebug) {
        throw $e;
      }
      // ignore integration logging errors
    }
  }
}
