<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Builder\UpdateAutomationController;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Automation\Engine\Validation\AutomationSchema;
use MailPoet\Validator\Builder;

class AutomationsPutEndpoint extends Endpoint {
  /** @var UpdateAutomationController */
  private $updateController;

  /** @var AutomationMapper */
  private $automationMapper;

  public function __construct(
    UpdateAutomationController $updateController,
    AutomationMapper $automationMapper
  ) {
    $this->updateController = $updateController;
    $this->automationMapper = $automationMapper;
  }

  public function handle(Request $request): Response {
    $data = $request->getParams();
    /** @var int $automationId */
    $automationId = $request->getParam('id');
    $automation = $this->updateController->updateAutomation(intval($automationId), $data);
    return new Response($this->automationMapper->buildAutomation($automation));
  }

  public static function getRequestSchema(): array {
    return [
      'id' => Builder::integer()->required(),
      'name' => Builder::string()->minLength(1),
      'status' => Builder::string(),
      'steps' => AutomationSchema::getStepsSchema(),
      'meta' => Builder::object(),
    ];
  }
}
