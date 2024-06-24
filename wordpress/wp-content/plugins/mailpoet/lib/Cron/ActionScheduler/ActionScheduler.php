<?php declare(strict_types = 1);

namespace MailPoet\Cron\ActionScheduler;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class ActionScheduler {
  public const GROUP_ID = 'mailpoet-cron';

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function scheduleRecurringAction(int $timestamp, int $interval_in_seconds, string $hook, array $args = [], bool $unique = true): int {
    return as_schedule_recurring_action($timestamp, $interval_in_seconds, $hook, $args, self::GROUP_ID, $unique);
  }

  public function scheduleImmediateSingleAction(string $hook, array $args = [], bool $unique = true): int {
    return as_schedule_single_action($this->wp->currentTime('timestamp', true), $hook, $args, self::GROUP_ID, $unique);
  }

  public function unscheduleAction(string $hook, array $args = []): ?int {
    $id = as_unschedule_action($hook, $args, self::GROUP_ID);
    return $id !== null ? intval($id) : null;
  }

  public function unscheduleAllCronActions(): void {
    // Passing only group to unschedule all by group
    as_unschedule_all_actions('', [], self::GROUP_ID);
  }

  public function hasScheduledAction(string $hook, array $args = []): bool {
    return as_has_scheduled_action($hook, $args, self::GROUP_ID);
  }
}
