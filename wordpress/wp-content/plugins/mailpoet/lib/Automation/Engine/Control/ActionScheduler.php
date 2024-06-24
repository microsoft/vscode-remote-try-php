<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use ActionScheduler_Action;

class ActionScheduler {
  private const GROUP_ID = 'mailpoet-automation';

  public function enqueue(string $hook, array $args = []): int {
    return as_enqueue_async_action($hook, $args, self::GROUP_ID);
  }

  public function schedule(int $timestamp, string $hook, array $args = []): int {
    return as_schedule_single_action($timestamp, $hook, $args, self::GROUP_ID);
  }

  public function hasScheduledAction(string $hook, array $args = []): bool {
    return as_has_scheduled_action($hook, $args, self::GROUP_ID);
  }

  /** @return ActionScheduler_Action[] */
  public function getScheduledActions(array $args = []): array {
    return as_get_scheduled_actions(array_merge($args, ['group' => self::GROUP_ID]));
  }

  public function unscheduleAction(string $hook, array $args = []): ?int {
    return as_unschedule_action($hook, $args, self::GROUP_ID);
  }
}
