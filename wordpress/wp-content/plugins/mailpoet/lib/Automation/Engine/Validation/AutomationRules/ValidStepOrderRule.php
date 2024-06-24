<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\SubjectTransformerHandler;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;

class ValidStepOrderRule implements AutomationNodeVisitor {
  /** @var Registry */
  private $registry;

  /** @var SubjectTransformerHandler */
  private $subjectTransformerHandler;

  public function __construct(
    Registry $registry,
    SubjectTransformerHandler $subjectTransformerHandler
  ) {
    $this->registry = $registry;
    $this->subjectTransformerHandler = $subjectTransformerHandler;
  }

  public function initialize(Automation $automation): void {
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    $registryStep = $this->registry->getStep($step->getKey());
    if (!$registryStep) {
      return;
    }

    // triggers don't require any subjects (they provide them)
    if ($step->getType() === Step::TYPE_TRIGGER) {
      return;
    }

    $requiredSubjectKeys = $registryStep->getSubjectKeys();
    if (!$requiredSubjectKeys) {
      return;
    }

    $subjectKeys = $this->subjectTransformerHandler->getSubjectKeysForAutomation($automation);
    $missingSubjectKeys = array_diff($requiredSubjectKeys, $subjectKeys);
    if (count($missingSubjectKeys) > 0) {
      throw Exceptions::missingRequiredSubjects($step, $missingSubjectKeys);
    }
  }

  public function complete(Automation $automation): void {
  }
}
