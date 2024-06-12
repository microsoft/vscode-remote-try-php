<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class ConsistentStepMapRule implements AutomationNodeVisitor {
  public const RULE_ID = 'consistent-step-map';

  public function initialize(Automation $automation): void {
    foreach ($automation->getSteps() as $id => $step) {
      if ((string)$id !== $step->getId()) {
        // translators: %1$s is the ID of the step, %2$s is its index in the steps object.
        throw Exceptions::automationStructureNotValid(
          sprintf(__("Step with ID '%1\$s' stored under a mismatched index '%2\$s'.", 'mailpoet'), $step->getId(), $id),
          self::RULE_ID
        );
      }
    }
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
  }

  public function complete(Automation $automation): void {
  }
}
