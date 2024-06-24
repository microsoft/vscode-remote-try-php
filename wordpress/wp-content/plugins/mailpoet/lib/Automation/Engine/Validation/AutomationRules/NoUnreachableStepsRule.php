<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class NoUnreachableStepsRule implements AutomationNodeVisitor {
  public const RULE_ID = 'no-unreachable-steps';

  /** @var AutomationNode[] */
  private $visitedNodes = [];

  public function initialize(Automation $automation): void {
    $this->visitedNodes = [];
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $this->visitedNodes[$node->getStep()->getId()] = $node;
  }

  public function complete(Automation $automation): void {
    if (count($this->visitedNodes) !== count($automation->getSteps())) {
      throw Exceptions::automationStructureNotValid(__('Unreachable steps found in automation graph', 'mailpoet'), self::RULE_ID);
    }
  }
}
