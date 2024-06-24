<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Analytics\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Exceptions\NotFoundException;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Controller\OverviewStatisticsController;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Entities\QueryWithCompare;
use MailPoet\Validator\Builder;

class OverviewEndpoint extends Endpoint {

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var OverviewStatisticsController */
  private $overviewStatisticsController;

  public function __construct(
    AutomationStorage $automationStorage,
    OverviewStatisticsController $overviewStatisticsController
  ) {
    $this->automationStorage = $automationStorage;
    $this->overviewStatisticsController = $overviewStatisticsController;
  }

  public function handle(Request $request): Response {
    $automation = $this->automationStorage->getAutomation((int)$request->getParam('id'));
    if (!$automation) {
      throw new NotFoundException(__('Automation not found', 'mailpoet'));
    }
    $query = QueryWithCompare::fromRequest($request);

    $result = $this->overviewStatisticsController->getStatisticsForAutomation($automation, $query);
    return new Response($result);
  }

  public static function getRequestSchema(): array {
    return [
      'id' => Builder::integer()->required(),
      'query' => QueryWithCompare::getRequestSchema(),
    ];
  }
}
