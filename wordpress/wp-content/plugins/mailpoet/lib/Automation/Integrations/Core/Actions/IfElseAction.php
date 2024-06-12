<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\FilterHandler;
use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\FilterGroup;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\Integration\ValidationException;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class IfElseAction implements Action {
  public const KEY = 'core:if-else';

  /** @var FilterHandler */
  private $filterHandler;

  public function __construct(
    FilterHandler $filterHandler
  ) {
    $this->filterHandler = $filterHandler;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation action title
    return __('If/Else', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }

  public function getSubjectKeys(): array {
    return [];
  }

  public function validate(StepValidationArgs $args): void {
    $step = $args->getStep();

    // validate next steps
    $nextSteps = $step->getNextSteps();
    if (count($nextSteps) !== 2) {
      throw ValidationException::create()->withError(
        'if_else_next_steps_count',
        __('If/Else action must have exactly two next steps.', 'mailpoet')
      );
    }

    // validate conditions
    $groups = $step->getFilters() ? $step->getFilters()->getGroups() : [];
    $conditions = array_map(function (FilterGroup $group) {
      return $group->getFilters();
    }, $groups);

    if (count($conditions) === 0) {
      throw ValidationException::create()->withError(
        'if_else_conditions_count',
        __('If/Else action must have at least one condition set.', 'mailpoet')
      );
    }
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {
    $matches = $this->filterHandler->matchesFilters($args);
    $controller->scheduleNextStepByIndex($matches ? 0 : 1);
  }
}
