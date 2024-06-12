<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class TriggersUnderRootRule implements AutomationNodeVisitor {
  public const RULE_ID = 'triggers-under-root';

  /** @var array<string, Step> $triggersMap */
  private $triggersMap = [];

  public function initialize(Automation $automation): void {
    $this->triggersMap = [];
    foreach ($automation->getSteps() as $step) {
      if ($step->getType() === 'trigger') {
        $this->triggersMap[$step->getId()] = $step;
      }
    }
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    if ($step->getType() === Step::TYPE_ROOT) {
      return;
    }

    foreach ($step->getNextStepIds() as $nextStepId) {
      if (isset($this->triggersMap[$nextStepId])) {
        throw Exceptions::automationStructureNotValid(__('Trigger must be a direct descendant of automation root', 'mailpoet'), self::RULE_ID);
      }
    }
  }

  public function complete(Automation $automation): void {
  }
}
