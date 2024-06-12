<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationGraph;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Step;

class AutomationNode {
  /** @var Step */
  private $step;

  /** @var array */
  private $parents;

  /* @param Step[] $parents */
  public function __construct(
    Step $step,
    array $parents
  ) {
    $this->step = $step;
    $this->parents = $parents;
  }

  public function getStep(): Step {
    return $this->step;
  }

  /** @return Step[] */
  public function getParents(): array {
    return $this->parents;
  }
}
