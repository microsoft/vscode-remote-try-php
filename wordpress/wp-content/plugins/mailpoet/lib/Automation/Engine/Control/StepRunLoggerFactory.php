<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Storage\AutomationRunLogStorage;

class StepRunLoggerFactory {
  /** @var AutomationRunLogStorage */
  private $automationRunLogStorage;

  /** @var Hooks */
  private $hooks;

  public function __construct(
    AutomationRunLogStorage $automationRunLogStorage,
    Hooks $hooks
  ) {
    $this->automationRunLogStorage = $automationRunLogStorage;
    $this->hooks = $hooks;
  }

  public function createLogger(int $runId, string $stepId, string $stepType, int $runNumber): StepRunLogger {
    return new StepRunLogger($this->automationRunLogStorage, $this->hooks, $runId, $stepId, $stepType, $runNumber);
  }
}
