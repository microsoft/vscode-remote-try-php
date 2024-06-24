<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Builder\DuplicateAutomationController;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Validator\Builder;

class AutomationsDuplicateEndpoint extends Endpoint {
  /** @var AutomationMapper */
  private $automationMapper;

  /** @var DuplicateAutomationController */
  private $duplicateController;

  public function __construct(
    DuplicateAutomationController $duplicateController,
    AutomationMapper $automationMapper
  ) {
    $this->automationMapper = $automationMapper;
    $this->duplicateController = $duplicateController;
  }

  public function handle(Request $request): Response {
    /** @var int $automationId */
    $automationId = $request->getParam('id');
    $automationId = intval($automationId);
    $duplicate = $this->duplicateController->duplicateAutomation($automationId);
    return new Response($this->automationMapper->buildAutomation($duplicate));
  }

  public static function getRequestSchema(): array {
    return [
      'id' => Builder::integer()->required(),
    ];
  }
}
