<?php declare(strict_types = 1);

namespace MailPoet\Cron\ActionScheduler;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;

class RemoteExecutorHandler {
  const AJAX_ACTION_NAME = 'mailpoet-cron-action-scheduler-run';

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function init(): void {
    $this->wp->addAction('wp_ajax_nopriv_' . self::AJAX_ACTION_NAME, [$this, 'runActionScheduler'], 0);
  }

  /**
   * Attempts to spawn Action Scheduler runner via ajax request
   * @see https://actionscheduler.org/perf/#increasing-initialisation-rate-of-runners
   */
  public function triggerExecutor(): void {
    $this->wp->addFilter('https_local_ssl_verify', '__return_false', 100);
    $this->wp->wpRemotePost($this->wp->adminUrl('admin-ajax.php'), [
      'method' => 'POST',
      'timeout' => 5,
      'redirection' => 5,
      'httpversion' => '1.0',
      'blocking' => false,
      'headers' => [],
      'body' => [
        'action' => self::AJAX_ACTION_NAME,
      ],
      'cookies' => [],
    ]);
  }

  public function runActionScheduler(): void {
    try {
      $this->wp->addFilter('action_scheduler_queue_runner_concurrent_batches', [$this, 'ensureConcurrency']);
      \ActionScheduler_QueueRunner::instance()->run();
      wp_die();
    } catch (\Exception $e) {
      $mySqlGoneAwayMessage = Helpers::mySqlGoneAwayExceptionHandler($e);
      if ($mySqlGoneAwayMessage) {
        throw new \Exception($mySqlGoneAwayMessage, 0, $e);
      }
      throw $e;
    }
  }

  /**
   * When triggering new runner at the end of a runner execution
   * we need to make sure the concurrency allows more one runner.
   */
  public function ensureConcurrency(int $concurrency): int {
    return ($concurrency) < 2 ? 2 : $concurrency;
  }
}
