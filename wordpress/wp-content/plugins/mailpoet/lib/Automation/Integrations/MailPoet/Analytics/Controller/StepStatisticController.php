<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Analytics\Controller;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationRun;
use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Storage\AutomationRunLogStorage;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;

class StepStatisticController {

  /** @var AutomationRunStorage */
  private $automationRunStorage;

  /** @var AutomationRunLogStorage */
  private $automationRunLogStorage;

  public function __construct(
    AutomationRunStorage $automationRunStorage,
    AutomationRunLogStorage $automationRunLogStorage
  ) {
    $this->automationRunStorage = $automationRunStorage;
    $this->automationRunLogStorage = $automationRunLogStorage;
  }

  public function getWaitingStatistics(Automation $automation, Query $query): array {
    $rawData = $this->automationRunStorage->getAutomationStepStatisticForTimeFrame(
      $automation->getId(),
      AutomationRun::STATUS_RUNNING,
      $query->getAfter(),
      $query->getBefore()
    );

    $data = [];
    foreach ($automation->getSteps() as $step) {
      foreach ($rawData as $rawDatum) {
        if ($rawDatum['next_step_id'] === $step->getId()) {
          $data[$step->getId()] = (int)$rawDatum['count'];
        }
      }
    }
    return $data;
  }

  public function getFailedStatistics(Automation $automation, Query $query): array {
    $rawData = $this->automationRunStorage->getAutomationStepStatisticForTimeFrame(
      $automation->getId(),
      AutomationRun::STATUS_FAILED,
      $query->getAfter(),
      $query->getBefore()
    );

    $data = [];
    foreach ($automation->getSteps() as $step) {
      foreach ($rawData as $rawDatum) {
        if ($rawDatum['next_step_id'] === $step->getId()) {
          $data[$step->getId()] = (int)$rawDatum['count'];
        }
      }
    }
    return $data;
  }

  public function getFlowStatistics(Automation $automation, Query $query): array {
    $statistics = $this->automationRunLogStorage->getAutomationRunStatisticsForAutomationInTimeFrame(
      $automation->getId(),
      AutomationRunLog::STATUS_COMPLETE,
      $query->getAfter(),
      $query->getBefore()
    );

    $data = [];

    foreach ($automation->getSteps() as $step) {
      if ($step->getType() === Step::TYPE_ROOT) {
        continue;
      }
      $data[$step->getId()] = 0;
      foreach ($statistics as $stat) {
        if ($stat['step_id'] === $step->getId()) {
          $data[$step->getId()] = (int)$stat['count'];
        }
      }
    }

    return $data;
  }
}
