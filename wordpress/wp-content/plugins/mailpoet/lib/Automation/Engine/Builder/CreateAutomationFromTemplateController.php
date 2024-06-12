<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\Validation\AutomationValidator;

class CreateAutomationFromTemplateController {
  /** @var AutomationStorage */
  private $storage;

  /** @var AutomationValidator */
  private $automationValidator;

  /** @var Registry */
  private $registry;

  public function __construct(
    AutomationStorage $storage,
    AutomationValidator $automationValidator,
    Registry $registry
  ) {
    $this->storage = $storage;
    $this->automationValidator = $automationValidator;
    $this->registry = $registry;
  }

  public function createAutomation(string $slug): Automation {
    $template = $this->registry->getTemplate($slug);
    if (!$template) {
      throw Exceptions::automationTemplateNotFound($slug);
    }

    $automation = $template->createAutomation();
    $this->automationValidator->validate($automation);
    $automationId = $this->storage->createAutomation($automation);
    $savedAutomation = $this->storage->getAutomation($automationId);
    if (!$savedAutomation) {
      throw new InvalidStateException('Automation not found.');
    }
    return $savedAutomation;
  }
}
