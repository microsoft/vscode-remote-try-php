<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationGraph;

if (!defined('ABSPATH')) exit;


use Generator;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Exceptions\UnexpectedValueException;

class AutomationWalker {
  /** @param AutomationNodeVisitor[] $visitors */
  public function walk(Automation $automation, array $visitors = []): void {
    $steps = $automation->getSteps();
    $root = $steps['root'] ?? null;
    if (!$root) {
      throw Exceptions::automationStructureNotValid(__("Automation must contain a 'root' step", 'mailpoet'), 'no-root');
    }

    foreach ($visitors as $visitor) {
      $visitor->initialize($automation);
    }

    foreach ($this->walkStepsDepthFirstPreOrder($steps, $root) as $record) {
      [$step, $parents] = $record;
      foreach ($visitors as $visitor) {
        $visitor->visitNode($automation, new AutomationNode($step, array_values($parents)));
      }
    }

    foreach ($visitors as $visitor) {
      $visitor->complete($automation);
    }
  }

  /**
   * @param array<string|int, Step> $steps
   * @return Generator<array{0: Step, 1: array<string|int, Step>}>
   */
  private function walkStepsDepthFirstPreOrder(array $steps, Step $root): Generator {
    /** @var array{0: Step, 1: array<string|int, Step>}[] $stack */
    $stack = [
      [$root, []],
    ];

    do {
      $record = array_pop($stack);
      if (!$record) {
        throw new InvalidStateException();
      }
      yield $record;
      [$step, $parents] = $record;

      foreach (array_reverse($step->getNextSteps()) as $nextStepData) {
        $nextStepId = $nextStepData->getId();
        if (!$nextStepId) {
          continue; // empty edge
        }
        $nextStep = $steps[$nextStepId] ?? null;
        if (!$nextStep) {
          throw $this->createStepNotFoundException($nextStepId, $step->getId());
        }

        $nextStepParents = array_merge($parents, [$step->getId() => $step]);
        if (isset($nextStepParents[$nextStepId])) {
          continue; // cycle detected, do not enter the path again
        }
        array_push($stack, [$nextStep, $nextStepParents]);
      }
    } while (count($stack) > 0);
  }

  private function createStepNotFoundException(string $stepId, string $parentStepId): UnexpectedValueException {
    return Exceptions::automationStructureNotValid(
      // translators: %1$s is ID of the step not found, %2$s is ID of the step that references it
      sprintf(
        __("Step with ID '%1\$s' not found (referenced from '%2\$s')", 'mailpoet'),
        $stepId,
        $parentStepId
      ),
      'step-not-found'
    );
  }
}
