<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class UnknownStepRule implements AutomationNodeVisitor {
  /** @var Registry */
  private $registry;

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var Automation|null|false */
  private $cachedExistingAutomation = false;

  public function __construct(
    Registry $registry,
    AutomationStorage $automationStorage
  ) {
    $this->registry = $registry;
    $this->automationStorage = $automationStorage;
  }

  public function initialize(Automation $automation): void {
    $this->cachedExistingAutomation = false;
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    $registryStep = $this->registry->getStep($step->getKey());

    // step not registered (e.g. plugin was deactivated) - allow saving it only if it hasn't changed
    if (!$registryStep) {
      $currentAutomation = $this->getCurrentAutomation($automation);
      $currentStep = $currentAutomation ? ($currentAutomation->getSteps()[$step->getId()] ?? null) : null;
      if (!$currentStep || $step->toArray() !== $currentStep->toArray()) {
        throw Exceptions::automationStepModifiedWhenUnknown($step);
      }
    }
  }

  public function complete(Automation $automation): void {
  }

  private function getCurrentAutomation(Automation $automation): ?Automation {
    try {
      $id = $automation->getId();
      if ($this->cachedExistingAutomation === false) {
        $this->cachedExistingAutomation = $this->automationStorage->getAutomation($id);
      }
    } catch (InvalidStateException $e) {
      // for new automations, no automation ID is set
      $this->cachedExistingAutomation = null;
    }
    return $this->cachedExistingAutomation;
  }
}
