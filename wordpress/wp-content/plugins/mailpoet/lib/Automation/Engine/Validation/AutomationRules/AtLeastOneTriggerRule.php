<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class AtLeastOneTriggerRule implements AutomationNodeVisitor {
  public const RULE_ID = 'at-least-one-trigger';

  /** @var bool */
  private $triggerFound = false;

  public function initialize(Automation $automation): void {
    $this->triggerFound = false;
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    if ($node->getStep()->getType() === Step::TYPE_TRIGGER) {
      $this->triggerFound = true;
    }
  }

  public function complete(Automation $automation): void {
    if (!$automation->needsFullValidation()) {
      return;
    }

    if ($this->triggerFound) {
      return;
    }
    throw Exceptions::automationStructureNotValid(__('There must be at least one trigger in the automation.', 'mailpoet'), self::RULE_ID);
  }
}
