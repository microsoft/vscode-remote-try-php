<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\Core\Actions\DelayAction;
use MailPoet\Automation\Integrations\Core\Actions\IfElseAction;

class CoreIntegration implements Integration {
  /** @var DelayAction */
  private $delayAction;

  /** @var WordPress */
  private $wordPress;

  /** @var IfElseAction */
  private $ifElseAction;

  public function __construct(
    DelayAction $delayAction,
    IfElseAction $ifElseAction,
    WordPress $wordPress
  ) {
    $this->delayAction = $delayAction;
    $this->ifElseAction = $ifElseAction;
    $this->wordPress = $wordPress;
  }

  public function register(Registry $registry): void {
    $registry->addAction($this->delayAction);
    $registry->addAction($this->ifElseAction);

    $registry->addFilter(new Filters\BooleanFilter());
    $registry->addFilter(new Filters\NumberFilter());
    $registry->addFilter(new Filters\IntegerFilter());
    $registry->addFilter(new Filters\StringFilter());
    $registry->addFilter(new Filters\DateTimeFilter($this->wordPress->wpTimezone()));
    $registry->addFilter(new Filters\EnumFilter());
    $registry->addFilter(new Filters\EnumArrayFilter());
  }
}
