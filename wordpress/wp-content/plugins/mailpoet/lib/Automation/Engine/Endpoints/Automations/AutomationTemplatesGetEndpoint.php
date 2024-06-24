<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Validator\Builder;

class AutomationTemplatesGetEndpoint extends Endpoint {
  /** @var Registry */
  private $registry;

  public function __construct(
    Registry $registry
  ) {
    $this->registry = $registry;
  }

  public function handle(Request $request): Response {
    /** @var string|null $category */
    $category = $request->getParam('category');
    $templates = array_values($this->registry->getTemplates($category ? strval($category) : null));
    return new Response(array_map(function (AutomationTemplate $automation) {
      return $automation->toArray();
    }, $templates));
  }

  public static function getRequestSchema(): array {
    return [
      'category' => Builder::string(),
    ];
  }
}
