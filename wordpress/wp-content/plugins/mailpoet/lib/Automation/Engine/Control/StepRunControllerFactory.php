<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;

class StepRunControllerFactory {
  /** @var StepScheduler */
  private $stepScheduler;

  public function __construct(
    StepScheduler $stepScheduler
  ) {
    $this->stepScheduler = $stepScheduler;
  }

  public function createController(StepRunArgs $args): StepRunController {
    return new StepRunController($this->stepScheduler, $args);
  }
}
