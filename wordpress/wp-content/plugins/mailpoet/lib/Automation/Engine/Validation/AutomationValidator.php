<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationWalker;
use MailPoet\Automation\Engine\Validation\AutomationRules\AtLeastOneTriggerRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\ConsistentStepMapRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\NoCycleRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\NoDuplicateEdgesRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\NoJoinRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\NoUnreachableStepsRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\TriggerNeedsToBeFollowedByActionRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\TriggersUnderRootRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\UnknownStepRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\ValidStepArgsRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\ValidStepFiltersRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\ValidStepOrderRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\ValidStepRule;
use MailPoet\Automation\Engine\Validation\AutomationRules\ValidStepValidationRule;

class AutomationValidator {
  /** @var AutomationWalker */
  private $automationWalker;

  /** @var ValidStepArgsRule */
  private $validStepArgsRule;

  /** @var ValidStepFiltersRule */
  private $validStepFiltersRule;

  /** @var ValidStepOrderRule */
  private $validStepOrderRule;

  /** @var ValidStepValidationRule */
  private $validStepValidationRule;

  /** @var UnknownStepRule */
  private $unknownStepRule;

  public function __construct(
    UnknownStepRule $unknownStepRule,
    ValidStepArgsRule $validStepArgsRule,
    ValidStepFiltersRule $validStepFiltersRule,
    ValidStepOrderRule $validStepOrderRule,
    ValidStepValidationRule $validStepValidationRule,
    AutomationWalker $automationWalker
  ) {
    $this->unknownStepRule = $unknownStepRule;
    $this->validStepArgsRule = $validStepArgsRule;
    $this->validStepFiltersRule = $validStepFiltersRule;
    $this->validStepOrderRule = $validStepOrderRule;
    $this->validStepValidationRule = $validStepValidationRule;
    $this->automationWalker = $automationWalker;
  }

  public function validate(Automation $automation): void {
    $this->automationWalker->walk($automation, [
      new NoUnreachableStepsRule(),
      new ConsistentStepMapRule(),
      new NoDuplicateEdgesRule(),
      new TriggersUnderRootRule(),
      new NoCycleRule(),
      new NoJoinRule(),
      $this->unknownStepRule,
      new AtLeastOneTriggerRule(),
      new TriggerNeedsToBeFollowedByActionRule(),
      new ValidStepRule([
        $this->validStepArgsRule,
        $this->validStepFiltersRule,
        $this->validStepOrderRule,
        $this->validStepValidationRule,
      ]),
    ]);
  }
}
