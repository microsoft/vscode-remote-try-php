<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Validation\AutomationValidator;
use MailPoet\Validator\Builder;

class AutomationTemplateGetEndpoint extends Endpoint {
  /** @var AutomationMapper */
  private $automationMapper;

  /** @var AutomationValidator */
  private $automationValidator;

  /** @var Registry */
  private $registry;

  public function __construct(
    AutomationMapper $automationMapper,
    AutomationValidator $automationValidator,
    Registry $registry
  ) {
    $this->registry = $registry;
    $this->automationValidator = $automationValidator;
    $this->automationMapper = $automationMapper;
  }

  public function handle(Request $request): Response {
    /** @var string|null $slug - for PHPStan because strval() doesn't accept a value of mixed */
    $slug = $request->getParam('slug');
    $slug = strval($slug);
    $template = $this->registry->getTemplate($slug);
    if (!$template) {
      throw Exceptions::automationTemplateNotFound($slug);
    }

    $automation = $template->createAutomation();
    $automation->setId(0);
    $this->automationValidator->validate($automation);

    $data = $template->toArray() + [
      'automation' => $this->automationMapper->buildAutomation($automation),
    ];
    return new Response($data);
  }

  public static function getRequestSchema(): array {
    return [
      'slug' => Builder::string()->required(),
    ];
  }
}
