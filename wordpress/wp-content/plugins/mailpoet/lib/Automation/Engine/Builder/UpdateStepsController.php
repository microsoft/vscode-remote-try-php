<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Registry;

class UpdateStepsController {
  /** @var Registry */
  private $registry;

  public function __construct(
    Registry $registry
  ) {
    $this->registry = $registry;
  }

  public function updateSteps(Automation $automation, array $data): Automation {
    $steps = [];
    foreach ($data as $index => $stepData) {
      $step = $this->processStep($stepData, $automation->getStep($stepData['id']));
      $steps[$index] = $step;
    }
    $automation->setSteps($steps);
    return $automation;
  }

  private function processStep(array $data, ?Step $existingStep): Step {
    $key = $data['key'];
    $step = $this->registry->getStep($key);
    if (!$step && $existingStep && $data !== $existingStep->toArray()) {
      throw Exceptions::automationStepNotFound($key);
    }
    return Step::fromArray($data);
  }
}
