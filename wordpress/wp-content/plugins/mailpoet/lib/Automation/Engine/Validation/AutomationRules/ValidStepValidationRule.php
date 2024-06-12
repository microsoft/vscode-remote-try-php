<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class ValidStepValidationRule implements AutomationNodeVisitor {
  /** @var Registry */
  private $registry;

  public function __construct(
    Registry $registry
  ) {
    $this->registry = $registry;
  }

  public function initialize(Automation $automation): void {
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    $registryStep = $this->registry->getStep($step->getKey());
    if (!$registryStep) {
      return;
    }

    $subjects = $this->collectSubjects($automation, $node->getParents());
    $args = new StepValidationArgs($automation, $step, $subjects);
    $registryStep->validate($args);
  }

  public function complete(Automation $automation): void {
  }

  /**
   * @param Step[] $parents
   * @return Subject<Payload>[]
   */
  private function collectSubjects(Automation $automation, array $parents): array {
    $triggers = array_filter($parents, function (Step $step) {
      return $step->getType() === Step::TYPE_TRIGGER;
    });

    $subjectKeys = [];
    foreach ($triggers as $trigger) {
      $registryTrigger = $this->registry->getTrigger($trigger->getKey());
      if (!$registryTrigger) {
        throw Exceptions::automationTriggerNotFound($automation->getId(), $trigger->getKey());
      }
      $subjectKeys = array_merge($subjectKeys, $registryTrigger->getSubjectKeys());
    }

    $subjects = [];
    foreach (array_unique($subjectKeys) as $key) {
      $subject = $this->registry->getSubject($key);
      if (!$subject) {
        throw Exceptions::subjectNotFound($key);
      }
      $subjects[] = $subject;
    }
    return $subjects;
  }
}
