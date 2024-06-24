<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\Integration\ValidationException;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class DelayAction implements Action {
  public const KEY = 'core:delay';

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation action title
    return _x('Delay', 'noun', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'delay' => Builder::integer()->required()->minimum(1),
      'delay_type' => Builder::string()->required()->pattern('^(MINUTES|DAYS|HOURS|WEEKS)$')->default('HOURS'),
    ]);
  }

  public function getSubjectKeys(): array {
    return [];
  }

  public function validate(StepValidationArgs $args): void {
    $seconds = $this->calculateSeconds($args->getStep());
    if ($seconds <= 0) {
      throw ValidationException::create()
        ->withError('delay', __('A delay must have a positive value', 'mailpoet'));
    }
    if ($seconds > 2 * YEAR_IN_SECONDS) {
      throw ValidationException::create()
        ->withError('delay', __("A delay can't be longer than two years", 'mailpoet'));
    }
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {
    if ($args->isFirstRun()) {
      $controller->scheduleProgress(time() + $this->calculateSeconds($args->getStep()));
    }
  }

  private function calculateSeconds(Step $step): int {
    $delay = (int)($step->getArgs()['delay'] ?? null);
    switch ($step->getArgs()['delay_type']) {
      case "MINUTES":
        return $delay * MINUTE_IN_SECONDS;
      case "HOURS":
        return $delay * HOUR_IN_SECONDS;
      case "DAYS":
        return $delay * DAY_IN_SECONDS;
      case "WEEKS":
        return $delay * WEEK_IN_SECONDS;
      default:
        return 0;
    }
  }
}
