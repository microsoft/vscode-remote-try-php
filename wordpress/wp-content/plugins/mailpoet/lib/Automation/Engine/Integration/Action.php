<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Integration;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\StepRunArgs;

interface Action extends Step {
  public function run(StepRunArgs $args, StepRunController $controller): void;
}
