<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\AutomationRun;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;

class StepScheduler {
  /** @var ActionScheduler */
  private $actionScheduler;

  /** @var AutomationRunStorage */
  private $automationRunStorage;

  public function __construct(
    ActionScheduler $actionScheduler,
    AutomationRunStorage $automationRunStorage
  ) {
    $this->actionScheduler = $actionScheduler;
    $this->automationRunStorage = $automationRunStorage;
  }

  public function scheduleProgress(StepRunArgs $args, int $timestamp = null): int {
    $runId = $args->getAutomationRun()->getId();
    $data = $this->getActionData($runId, $args->getStep()->getId(), $args->getRunNumber() + 1);
    return $this->scheduleStepAction($data, $timestamp);
  }

  public function scheduleNextStep(StepRunArgs $args, int $timestamp = null): int {
    $step = $args->getStep();
    $nextSteps = $step->getNextSteps();

    // complete the automation run if there are no more steps
    if (count($nextSteps) === 0) {
      $this->completeAutomationRun($args);
      return 0;
    }

    if (count($nextSteps) > 1) {
      throw Exceptions::nextStepNotScheduled($step->getId());
    }

    return $this->scheduleNextStepByIndex($args, 0, $timestamp);
  }

  public function scheduleNextStepByIndex(StepRunArgs $args, int $nextStepIndex, int $timestamp = null): int {
    $step = $args->getStep();
    $nextStep = $step->getNextSteps()[$nextStepIndex] ?? null;
    if (!$nextStep) {
      throw Exceptions::nextStepNotFound($step->getId(), $nextStepIndex);
    }

    $runId = $args->getAutomationRun()->getId();
    $nextStepId = $nextStep->getId();
    if (!$nextStepId) {
      $this->completeAutomationRun($args);
      return 0;
    }

    $data = $this->getActionData($runId, $nextStepId);
    $id = $this->scheduleStepAction($data, $timestamp);
    $this->automationRunStorage->updateNextStep($runId, $nextStepId);
    return $id;
  }

  public function hasScheduledNextStep(StepRunArgs $args): bool {
    $runId = $args->getAutomationRun()->getId();
    foreach ($args->getStep()->getNextStepIds() as $nextStepId) {
      $data = $this->getActionData($runId, $nextStepId);
      $hasStep = $this->actionScheduler->hasScheduledAction(Hooks::AUTOMATION_STEP, $data);
      if ($hasStep) {
        return true;
      }

      // BC for old steps without run number
      unset($data[0]['run_number']);
      $hasStep = $this->actionScheduler->hasScheduledAction(Hooks::AUTOMATION_STEP, $data);
      if ($hasStep) {
        return true;
      }
    }
    return false;
  }

  public function hasScheduledProgress(StepRunArgs $args): bool {
    $runId = $args->getAutomationRun()->getId();
    $data = $this->getActionData($runId, $args->getStep()->getId(), $args->getRunNumber() + 1);
    return $this->actionScheduler->hasScheduledAction(Hooks::AUTOMATION_STEP, $data);
  }

  public function hasScheduledStep(StepRunArgs $args): bool {
    return $this->hasScheduledNextStep($args) || $this->hasScheduledProgress($args);
  }

  private function scheduleStepAction(array $data, int $timestamp = null): int {
    return $timestamp === null
      ? $this->actionScheduler->enqueue(Hooks::AUTOMATION_STEP, $data)
      : $this->actionScheduler->schedule($timestamp, Hooks::AUTOMATION_STEP, $data);
  }

  private function completeAutomationRun(StepRunArgs $args): void {
    $runId = $args->getAutomationRun()->getId();
    $this->automationRunStorage->updateNextStep($runId, null);
    $this->automationRunStorage->updateStatus($runId, AutomationRun::STATUS_COMPLETE);
  }

  private function getActionData(int $runId, string $stepId, int $runNumber = 1): array {
    return [
      [
        'automation_run_id' => $runId,
        'step_id' => $stepId,
        'run_number' => $runNumber,
      ],
    ];
  }
}
