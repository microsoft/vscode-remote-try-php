<?php declare(strict_types = 1);

namespace MailPoet\Cron\ActionScheduler\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\ActionScheduler\ActionScheduler;
use MailPoet\Cron\ActionScheduler\RemoteExecutorHandler;
use MailPoet\Cron\Triggers\WordPress;
use MailPoet\WP\Functions as WPFunctions;

class DaemonTrigger {
  const NAME = 'mailpoet/cron/daemon-trigger';
  const TRIGGER_RUN_INTERVAL = 120; // 2 minutes

  /** @var WPFunctions */
  private $wp;

  /** @var WordPress */
  private $wordpressTrigger;

  /** @var RemoteExecutorHandler */
  private $remoteExecutorHandler;

  /** @var ActionScheduler */
  private $actionScheduler;

  public function __construct(
    WPFunctions $wp,
    WordPress $wordpressTrigger,
    RemoteExecutorHandler $remoteExecutorHandler,
    ActionScheduler $actionScheduler
  ) {
    $this->wp = $wp;
    $this->wordpressTrigger = $wordpressTrigger;
    $this->remoteExecutorHandler = $remoteExecutorHandler;
    $this->actionScheduler = $actionScheduler;
  }

  public function init() {
    $this->wp->addAction(self::NAME, [$this, 'process']);
    if (!$this->actionScheduler->hasScheduledAction(self::NAME)) {
      $this->actionScheduler->scheduleRecurringAction($this->wp->currentTime('timestamp', true), self::TRIGGER_RUN_INTERVAL, self::NAME);
    }
  }

  /**
   * It checks if there are scheduled tasks to execute.
   * In case there are tasks to do, it schedules a daemon-run action.
   */
  public function process(): void {
    $hasJobsToDo = $this->wordpressTrigger->checkExecutionRequirements();
    if (!$hasJobsToDo) {
      $this->actionScheduler->unscheduleAction(DaemonRun::NAME);
      return;
    }
    if ($this->actionScheduler->hasScheduledAction(DaemonRun::NAME)) {
      return;
    }
    // Schedule immediate action for execution of the daemon
    $this->actionScheduler->scheduleImmediateSingleAction(DaemonRun::NAME);
    $this->remoteExecutorHandler->triggerExecutor();
  }
}
