<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Data\Step;

class Hooks {
  /** @var WordPress */
  private $wordPress;

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  public const INITIALIZE = 'mailpoet/automation/initialize';
  public const API_INITIALIZE = 'mailpoet/automation/api/initialize';
  public const TRIGGER = 'mailpoet/automation/trigger';
  public const AUTOMATION_STEP = 'mailpoet/automation/step';

  public const EDITOR_BEFORE_LOAD = 'mailpoet/automation/editor/before_load';

  public const AUTOMATION_BEFORE_SAVE = 'mailpoet/automation/before_save';
  public const AUTOMATION_STEP_BEFORE_SAVE = 'mailpoet/automation/step/before_save';

  public const AUTOMATION_STEP_LOG_AFTER_RUN = 'mailpoet/automation/step/log_after_run';

  public const AUTOMATION_RUN_CREATE = 'mailpoet/automation/run/create';

  public function doAutomationBeforeSave(Automation $automation): void {
    $this->wordPress->doAction(self::AUTOMATION_BEFORE_SAVE, $automation);
  }

  public function doAutomationStepBeforeSave(Step $step, Automation $automation): void {
    $this->wordPress->doAction(self::AUTOMATION_STEP_BEFORE_SAVE, $step, $automation);
  }

  public function doAutomationStepByKeyBeforeSave(Step $step, Automation $automation): void {
    $this->wordPress->doAction(self::AUTOMATION_STEP_BEFORE_SAVE . '/key=' . $step->getKey(), $step, $automation);
  }

  public function doAutomationStepAfterRun(AutomationRunLog $automationRunLog): void {
    $this->wordPress->doAction(self::AUTOMATION_STEP_LOG_AFTER_RUN, $automationRunLog);
  }
}
