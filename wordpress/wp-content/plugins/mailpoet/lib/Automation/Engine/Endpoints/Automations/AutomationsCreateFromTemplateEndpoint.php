<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Builder\CreateAutomationFromTemplateController;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Validator\Builder;

class AutomationsCreateFromTemplateEndpoint extends Endpoint {
  /** @var CreateAutomationFromTemplateController */
  private $createAutomationFromTemplateController;

  /** @var AutomationMapper */
  private $automationMapper;

  public function __construct(
    CreateAutomationFromTemplateController $createAutomationFromTemplateController,
    AutomationMapper $automationMapper
  ) {
    $this->createAutomationFromTemplateController = $createAutomationFromTemplateController;
    $this->automationMapper = $automationMapper;
  }

  public function handle(Request $request): Response {
    $automation = $this->createAutomationFromTemplateController->createAutomation((string)$request->getParam('slug'));
    return new Response($this->automationMapper->buildAutomation($automation));
  }

  public static function getRequestSchema(): array {
    return [
      'slug' => Builder::string()->required(),
    ];
  }
}
