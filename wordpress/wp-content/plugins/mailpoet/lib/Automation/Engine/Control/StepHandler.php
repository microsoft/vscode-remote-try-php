<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use Exception;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationRun;
use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\SubjectEntry;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\WordPress;
use Throwable;

class StepHandler {
  /** @var SubjectLoader */
  private $subjectLoader;

  /** @var WordPress */
  private $wordPress;

  /** @var AutomationRunStorage */
  private $automationRunStorage;

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var Registry */
  private $registry;

  /** @var StepRunControllerFactory */
  private $stepRunControllerFactory;

  /** @var StepRunLoggerFactory */
  private $stepRunLoggerFactory;

  /** @var StepScheduler */
  private $stepScheduler;

  public function __construct(
    SubjectLoader $subjectLoader,
    WordPress $wordPress,
    AutomationRunStorage $automationRunStorage,
    AutomationStorage $automationStorage,
    Registry $registry,
    StepRunControllerFactory $stepRunControllerFactory,
    StepRunLoggerFactory $stepRunLoggerFactory,
    StepScheduler $stepScheduler
  ) {
    $this->subjectLoader = $subjectLoader;
    $this->wordPress = $wordPress;
    $this->automationRunStorage = $automationRunStorage;
    $this->automationStorage = $automationStorage;
    $this->registry = $registry;
    $this->stepRunControllerFactory = $stepRunControllerFactory;
    $this->stepRunLoggerFactory = $stepRunLoggerFactory;
    $this->stepScheduler = $stepScheduler;
  }

  public function initialize(): void {
    $this->wordPress->addAction(Hooks::AUTOMATION_STEP, [$this, 'handle']);
  }

  /** @param mixed $args */
  public function handle($args): void {
    // TODO: better args validation
    if (!is_array($args) || !isset($args['automation_run_id']) || !array_key_exists('step_id', $args)) {
      throw new InvalidStateException();
    }

    $runId = (int)$args['automation_run_id'];
    $stepId = (string)$args['step_id'];
    $runNumber = (int)($args['run_number'] ?? 1);

    // BC — complete automation run if "step_id" is empty (was nullable in the past)
    if (!$stepId) {
      $this->automationRunStorage->updateStatus($runId, AutomationRun::STATUS_COMPLETE);
      return;
    }

    $logger = $this->stepRunLoggerFactory->createLogger($runId, $stepId, AutomationRunLog::TYPE_ACTION, $runNumber);
    $logger->logStart();
    try {
      $this->handleStep($runId, $stepId, $runNumber, $logger);
    } catch (Throwable $e) {
      $status = $e instanceof InvalidStateException && $e->getErrorCode() === 'mailpoet_automation_not_active'
        ? AutomationRun::STATUS_CANCELLED
        : AutomationRun::STATUS_FAILED;
      $this->automationRunStorage->updateStatus((int)$args['automation_run_id'], $status);
      $logger->logFailure($e);

      // Action Scheduler catches only Exception instances, not other errors.
      // We need to convert them to exceptions to be processed and logged.
      if (!$e instanceof Exception) {
        throw new Exception($e->getMessage(), intval($e->getCode()), $e);
      }
      throw $e;
    } finally {
      $this->postProcessAutomationRun($runId);
    }
  }

  private function handleStep(int $runId, string $stepId, int $runNumber, StepRunLogger $logger): void {
    $automationRun = $this->automationRunStorage->getAutomationRun($runId);
    if (!$automationRun) {
      throw Exceptions::automationRunNotFound($runId);
    }

    if ($automationRun->getStatus() !== AutomationRun::STATUS_RUNNING) {
      throw Exceptions::automationRunNotRunning($runId, $automationRun->getStatus());
    }

    $automation = $this->automationStorage->getAutomation($automationRun->getAutomationId(), $automationRun->getVersionId());
    if (!$automation) {
      throw Exceptions::automationVersionNotFound($automationRun->getAutomationId(), $automationRun->getVersionId());
    }

    if (!in_array($automation->getStatus(), [Automation::STATUS_ACTIVE, Automation::STATUS_DEACTIVATING], true)) {
      throw Exceptions::automationNotActive($automationRun->getAutomationId());
    }

    $stepData = $automation->getStep($stepId);
    if (!$stepData) {
      throw Exceptions::automationStepNotFound($stepId);
    }

    $logger->logStepData($stepData);

    $step = $this->registry->getStep($stepData->getKey());
    if (!$step instanceof Action) {
      throw new InvalidStateException();
    }

    $requiredSubjects = $step->getSubjectKeys();
    $subjectEntries = $this->getSubjectEntries($automationRun, $requiredSubjects);
    $args = new StepRunArgs($automation, $automationRun, $stepData, $subjectEntries, $runNumber);
    $validationArgs = new StepValidationArgs($automation, $stepData, array_map(function (SubjectEntry $entry) {
      return $entry->getSubject();
    }, $subjectEntries));

    $step->validate($validationArgs);
    $step->run($args, $this->stepRunControllerFactory->createController($args));

    // schedule next step if not scheduled by action
    if (!$this->stepScheduler->hasScheduledStep($args)) {
      $this->stepScheduler->scheduleNextStep($args);
    }

    // logging
    if ($this->stepScheduler->hasScheduledProgress($args)) {
      $logger->logProgress();
    } else {
      $logger->logSuccess();
    }
  }

  /** @return SubjectEntry<Subject<Payload>>[] */
  private function getSubjectEntries(AutomationRun $automationRun, array $requiredSubjectKeys): array {
    $subjectDataMap = [];
    foreach ($automationRun->getSubjects() as $data) {
      $subjectDataMap[$data->getKey()] = array_merge($subjectDataMap[$data->getKey()] ?? [], [$data]);
    }

    $subjectEntries = [];
    foreach ($requiredSubjectKeys as $key) {
      $subjectData = $subjectDataMap[$key] ?? null;
      if (!$subjectData) {
        throw Exceptions::subjectDataNotFound($key, $automationRun->getId());
      }
    }
    foreach ($subjectDataMap as $subjectData) {
      foreach ($subjectData as $data) {
        $subjectEntries[] = $this->subjectLoader->getSubjectEntry($data);
      }
    }
    return $subjectEntries;
  }

  private function postProcessAutomationRun(int $automationRunId): void {
    $automationRun = $this->automationRunStorage->getAutomationRun($automationRunId);
    if (!$automationRun) {
      return;
    }
    $automation = $this->automationStorage->getAutomation($automationRun->getAutomationId());
    if (!$automation) {
      return;
    }
    $this->postProcessAutomation($automation);
  }

  private function postProcessAutomation(Automation $automation): void {
    if ($automation->getStatus() === Automation::STATUS_DEACTIVATING) {
      $activeRuns = $this->automationRunStorage->getCountForAutomation($automation, AutomationRun::STATUS_RUNNING);

      // Set a deactivating Automation to draft once all automation runs are finished.
      if ($activeRuns === 0) {
        $automation->setStatus(Automation::STATUS_DRAFT);
        $this->automationStorage->updateAutomation($automation);
      }
    }
  }
}
