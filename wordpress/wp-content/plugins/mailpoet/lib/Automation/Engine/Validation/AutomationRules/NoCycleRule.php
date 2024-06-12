<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class NoCycleRule implements AutomationNodeVisitor {
  public const RULE_ID = 'no-cycle';

  public function initialize(Automation $automation): void {
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    $parents = $node->getParents();
    $parentIdsMap = array_combine(
      array_map(function (Step $parent) {
        return $parent->getId();
      }, $node->getParents()),
      $parents
    ) ?: [];

    foreach ($step->getNextStepIds() as $nextStepId) {
      if ($nextStepId === $step->getId() || isset($parentIdsMap[$nextStepId])) {
        throw Exceptions::automationStructureNotValid(__('Cycle found in automation graph', 'mailpoet'), self::RULE_ID);
      }
    }
  }

  public function complete(Automation $automation): void {
  }
}
