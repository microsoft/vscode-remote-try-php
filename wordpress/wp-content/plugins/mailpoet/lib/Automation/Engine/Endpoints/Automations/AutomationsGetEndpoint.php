<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Validator\Builder;

class AutomationsGetEndpoint extends Endpoint {
  /** @var AutomationMapper */
  private $automationMapper;

  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    AutomationMapper $automationMapper,
    AutomationStorage $automationStorage
  ) {
    $this->automationMapper = $automationMapper;
    $this->automationStorage = $automationStorage;
  }

  public function handle(Request $request): Response {
    $status = $request->getParam('status') ? (array)$request->getParam('status') : null;
    $automations = $this->automationStorage->getAutomations($status);
    return new Response($this->automationMapper->buildAutomationList($automations));
  }

  public static function getRequestSchema(): array {
    return [
      'status' => Builder::array(Builder::string()),
    ];
  }
}
