<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationGraph;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;

interface AutomationNodeVisitor {
  public function initialize(Automation $automation): void;

  public function visitNode(Automation $automation, AutomationNode $node): void;

  public function complete(Automation $automation): void;
}
