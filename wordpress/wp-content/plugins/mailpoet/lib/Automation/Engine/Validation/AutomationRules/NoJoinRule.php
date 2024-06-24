<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class NoJoinRule implements AutomationNodeVisitor {
  public const RULE_ID = 'no-join';

  /** @var array<string|int, Step[]> */
  private $directParentMap = [];

  public function initialize(Automation $automation): void {
    $this->directParentMap = [];
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    foreach ($step->getNextStepIds() as $nextStepId) {
      $this->directParentMap[$nextStepId] = array_merge($this->directParentMap[$nextStepId] ?? [], [$step]);
    }

    if (count($this->directParentMap[$step->getId()] ?? []) > 1) {
      throw Exceptions::automationStructureNotValid(__('Path join found in automation graph', 'mailpoet'), self::RULE_ID);
    }
  }

  public function complete(Automation $automation): void {
  }
}
