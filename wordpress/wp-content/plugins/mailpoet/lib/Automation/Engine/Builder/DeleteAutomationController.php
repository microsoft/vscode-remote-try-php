<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Storage\AutomationStorage;

class DeleteAutomationController {
  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    AutomationStorage $automationStorage
  ) {
    $this->automationStorage = $automationStorage;
  }

  public function deleteAutomation(int $id): Automation {
    $automation = $this->automationStorage->getAutomation($id);
    if (!$automation) {
      throw Exceptions::automationNotFound($id);
    }

    if ($automation->getStatus() !== Automation::STATUS_TRASH) {
      throw Exceptions::automationNotTrashed($id);
    }

    $this->automationStorage->deleteAutomation($automation);
    return $automation;
  }
}
