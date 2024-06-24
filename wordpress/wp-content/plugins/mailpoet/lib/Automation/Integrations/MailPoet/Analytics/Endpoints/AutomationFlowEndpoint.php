<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Analytics\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions\NotFoundException;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Automation\Engine\Storage\AutomationStatisticsStorage;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\AutomationTimeSpanController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\StepStatisticController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\Query;
use MailPoet\Validator\Builder;

class AutomationFlowEndpoint extends Endpoint {

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var AutomationStatisticsStorage */
  private $automationStatisticsStorage;

  /** @var AutomationMapper */
  private $automationMapper;

  /** @var AutomationTimeSpanController */
  private $automationTimeSpanController;

  /** @var StepStatisticController */
  private $stepStatisticController;

  public function __construct(
    AutomationStorage $automationStorage,
    AutomationStatisticsStorage $automationStatisticsStorage,
    AutomationMapper $automationMapper,
    AutomationTimeSpanController $automationTimeSpanController,
    StepStatisticController $stepStatisticController
  ) {
    $this->automationStorage = $automationStorage;
    $this->automationStatisticsStorage = $automationStatisticsStorage;
    $this->automationMapper = $automationMapper;
    $this->automationTimeSpanController = $automationTimeSpanController;
    $this->stepStatisticController = $stepStatisticController;
  }

  public function handle(Request $request): Response {
    $automation = $this->automationStorage->getAutomation(absint($request->getParam('id')));
    if (!$automation) {
      throw new NotFoundException(__('Automation not found', 'mailpoet'));
    }
    $query = Query::fromRequest($request);
    $automations = $this->automationTimeSpanController->getAutomationsInTimespan($automation, $query->getAfter(), $query->getBefore());
    if (!count($automations)) {
      throw new NotFoundException(__('The automation did not exist in the selected time span', 'mailpoet'));
    }
    $automation = current($automations);
    $shortStatistics = $this->automationStatisticsStorage->getAutomationStats(
      $automation->getId(),
      null,
      $query->getAfter(),
      $query->getBefore()
    );

    $waitingData = $this->stepStatisticController->getWaitingStatistics($automation, $query);
    $failedData = $this->stepStatisticController->getFailedStatistics($automation, $query);
    try {
      $flowData = $this->stepStatisticController->getFlowStatistics($automation, $query);
    } catch (\Throwable $e) {
      return new Response([$e->getMessage()], 500);
    }
    $stepData = [
      'total' => $shortStatistics->getEntered(),
    ];
    if ($waitingData) {
      $stepData['waiting'] = $waitingData;
    }
    if ($failedData) {
      $stepData['failed'] = $failedData;
    }
    if ($flowData) {
      $stepData['flow'] = $flowData;
    }

    $data = [
      'automation' => $this->automationMapper->buildAutomation($automation, $shortStatistics),
      'step_data' => $stepData,
      'tree_is_inconsistent' => !$this->isTreeConsistent(...$automations),
    ];
    return new Response($data);
  }

  private function isTreeConsistent(Automation ...$automations): bool {
    if (count($automations) === 1) {
      return true;
    }
    $stepIds = array_map(function (Automation $automation) {
      return array_keys($automation->getSteps());
    }, $automations);
    $compareTo = array_shift($stepIds);
    if (!$compareTo) {
      return true;
    }
    foreach ($stepIds as $stepId) {
      if (count(array_diff($stepId, $compareTo)) !== 0) {
        return false;
      }
    }
    return true;
  }

  public static function getRequestSchema(): array {
    return [
      'id' => Builder::integer()->required(),
      'query' => Query::getRequestSchema(),
    ];
  }
}
