<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use ActionScheduler_CanceledAction;
use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Storage\AutomationRunLogStorage;

class AutomationController {
  private ActionScheduler $actionScheduler;
  private AutomationRunLogStorage $automationRunLogStorage;

  public function __construct(
    ActionScheduler $actionScheduler,
    AutomationRunLogStorage $automationRunLogStorage
  ) {
    $this->actionScheduler = $actionScheduler;
    $this->automationRunLogStorage = $automationRunLogStorage;
  }

  public function enqueueProgress(int $runId, string $stepId): void {
    $log = $this->automationRunLogStorage->getAutomationRunLogByRunAndStepId($runId, $stepId);
    if (!$log) {
      throw Exceptions::stepNotStarted($stepId, $runId);
    }

    if ($log->getStatus() !== AutomationRunLog::STATUS_RUNNING) {
      throw Exceptions::stepNotRunning($stepId, $log->getStatus(), $runId);
    }

    $runNumber = $log->getRunNumber() + 1;
    $args = [
      'automation_run_id' => $runId,
      'step_id' => $stepId,
      'run_number' => $runNumber,
    ];

    // if a pending action exists, unschedule it
    $this->actionScheduler->unscheduleAction(Hooks::AUTOMATION_STEP, [$args]);

    // if an action still exists (pending, in-progress, complete, failed), it's an error
    $actions = $this->actionScheduler->getScheduledActions(['hook' => Hooks::AUTOMATION_STEP, 'args' => [$args]]);
    $processedActions = array_filter($actions, function ($action) {
      return !$action instanceof ActionScheduler_CanceledAction;
    });
    if (count($processedActions) > 0) {
      throw Exceptions::stepActionProcessed($stepId, $runId, $runNumber);
    }

    $this->actionScheduler->enqueue(Hooks::AUTOMATION_STEP, [$args]);
  }
}
