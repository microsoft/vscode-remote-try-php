<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class NoDuplicateEdgesRule implements AutomationNodeVisitor {
  public const RULE_ID = 'no-duplicate-edges';

  public function initialize(Automation $automation): void {
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $visitedNextStepIdsMap = [];
    foreach ($node->getStep()->getNextStepIds() as $nextStepId) {
      if (isset($visitedNextStepIdsMap[$nextStepId])) {
        throw Exceptions::automationStructureNotValid(__('Duplicate next step definition found', 'mailpoet'), self::RULE_ID);
      }
      $visitedNextStepIdsMap[$nextStepId] = true;
    }
  }

  public function complete(Automation $automation): void {
  }
}
