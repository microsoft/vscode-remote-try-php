<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\AutomationRun;
use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\WordPress;

class TriggerHandler {
  /** @var AutomationStorage */
  private $automationStorage;

  /** @var AutomationRunStorage */
  private $automationRunStorage;

  /** @var SubjectLoader */
  private $subjectLoader;

  /** @var SubjectTransformerHandler */
  private $subjectTransformerHandler;

  /** @var FilterHandler */
  private $filterHandler;

  /** @var StepScheduler */
  private $stepScheduler;

  /** @var StepRunLoggerFactory */
  private $stepRunLoggerFactory;

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    AutomationStorage $automationStorage,
    AutomationRunStorage $automationRunStorage,
    SubjectLoader $subjectLoader,
    SubjectTransformerHandler $subjectTransformerHandler,
    FilterHandler $filterHandler,
    StepScheduler $stepScheduler,
    StepRunLoggerFactory $stepRunLoggerFactory,
    WordPress $wordPress
  ) {
    $this->automationStorage = $automationStorage;
    $this->automationRunStorage = $automationRunStorage;
    $this->subjectLoader = $subjectLoader;
    $this->subjectTransformerHandler = $subjectTransformerHandler;
    $this->filterHandler = $filterHandler;
    $this->stepScheduler = $stepScheduler;
    $this->stepRunLoggerFactory = $stepRunLoggerFactory;
    $this->wordPress = $wordPress;
  }

  public function initialize(): void {
    $this->wordPress->addAction(Hooks::TRIGGER, [$this, 'processTrigger'], 10, 2);
  }

  /** @param Subject[] $subjects */
  public function processTrigger(Trigger $trigger, array $subjects): void {
    $automations = $this->automationStorage->getActiveAutomationsByTrigger($trigger);
    if (!$automations) {
      return;
    }

    // expand all subject transformations and load subject entries
    $subjects = $this->subjectTransformerHandler->getAllSubjects($subjects);
    $subjectEntries = $this->subjectLoader->getSubjectsEntries($subjects);

    foreach ($automations as $automation) {
      $step = $automation->getTrigger($trigger->getKey());
      if (!$step) {
        throw Exceptions::automationTriggerNotFound($automation->getId(), $trigger->getKey());
      }

      $automationRun = new AutomationRun($automation->getId(), $automation->getVersionId(), $trigger->getKey(), $subjects);
      $stepRunArgs = new StepRunArgs($automation, $automationRun, $step, $subjectEntries, 1);

      $match = false;
      try {
        $match = $this->filterHandler->matchesFilters($stepRunArgs);
      } catch (Exceptions\Exception $e) {
        // failed filter evaluation won't match
        ;
      }
      if (!$match) {
        continue;
      }

      $createAutomationRun = $trigger->isTriggeredBy($stepRunArgs);
      $createAutomationRun = $this->wordPress->applyFilters(
        Hooks::AUTOMATION_RUN_CREATE,
        $createAutomationRun,
        $stepRunArgs
      );
      if (!$createAutomationRun) {
        continue;
      }


      $automationRunId = $this->automationRunStorage->createAutomationRun($automationRun);
      $automationRun->setId($automationRunId);
      $this->stepScheduler->scheduleNextStep($stepRunArgs);

      $logger = $this->stepRunLoggerFactory->createLogger($automationRunId, $step->getId(), AutomationRunLog::TYPE_TRIGGER, 1);
      $logger->logStepData($step);
      $logger->logSuccess();
    }
  }
}
