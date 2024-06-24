<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;

class StepRunController {
  /** @var StepScheduler */
  private $stepScheduler;

  /** @var StepRunArgs */
  private $stepRunArgs;

  public function __construct(
    StepScheduler $stepScheduler,
    StepRunArgs $stepRunArgs
  ) {
    $this->stepScheduler = $stepScheduler;
    $this->stepRunArgs = $stepRunArgs;
  }

  public function scheduleProgress(int $timestamp = null): int {
    return $this->stepScheduler->scheduleProgress($this->stepRunArgs, $timestamp);
  }

  public function scheduleNextStep(int $timestamp = null): int {
    return $this->stepScheduler->scheduleNextStep($this->stepRunArgs, $timestamp);
  }

  public function scheduleNextStepByIndex(int $nextStepIndex, int $timestamp = null): int {
    return $this->stepScheduler->scheduleNextStepByIndex($this->stepRunArgs, $nextStepIndex, $timestamp);
  }

  public function hasScheduledNextStep(): bool {
    return $this->stepScheduler->hasScheduledNextStep($this->stepRunArgs);
  }
}
