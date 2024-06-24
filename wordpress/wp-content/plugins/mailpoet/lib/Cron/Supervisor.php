<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron;

if (!defined('ABSPATH')) exit;


class Supervisor {
  public $daemon;
  public $token;

  /** @var CronHelper */
  private $cronHelper;

  public function __construct(
    CronHelper $cronHelper
  ) {
    $this->cronHelper = $cronHelper;
  }

  public function init() {
    $this->token = $this->cronHelper->createToken();
    $this->daemon = $this->getDaemon();
  }

  public function checkDaemon() {
    $daemon = $this->daemon;
    $updatedAt = $daemon ? (int)$daemon['updated_at'] : 0;
    $executionTimeoutExceeded =
      (time() - $updatedAt) >= $this->cronHelper->getDaemonExecutionTimeout();
    $daemonIsInactive =
      isset($daemon['status']) && $daemon['status'] === CronHelper::DAEMON_STATUS_INACTIVE;
    if ($executionTimeoutExceeded || $daemonIsInactive) {
      $this->cronHelper->restartDaemon($this->token);
      return $this->runDaemon();
    }
    return $daemon;
  }

  public function runDaemon() {
    $this->cronHelper->accessDaemon($this->token);
    $daemon = $this->cronHelper->getDaemon();
    return $daemon;
  }

  public function getDaemon() {
    $daemon = $this->cronHelper->getDaemon();
    if (!$daemon) {
      $this->cronHelper->createDaemon($this->token);
      return $this->runDaemon();
    }
    return $daemon;
  }
}
