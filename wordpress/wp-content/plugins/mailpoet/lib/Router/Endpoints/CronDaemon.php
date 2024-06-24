<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Router\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Cron\CronHelper;
use MailPoet\Cron\DaemonHttpRunner;

class CronDaemon {
  const ENDPOINT = 'cron_daemon';
  const ACTION_RUN = 'run';
  const ACTION_PING = 'ping';
  const ACTION_PING_RESPONSE = 'pingResponse';
  public $allowedActions = [
    self::ACTION_RUN,
    self::ACTION_PING,
    self::ACTION_PING_RESPONSE,
  ];
  public $data;
  public $permissions = [
    'global' => AccessControl::NO_ACCESS_RESTRICTION,
  ];

  /** @var DaemonHttpRunner */
  private $daemonRunner;

  /** @var CronHelper */
  private $cronHelper;

  public function __construct(
    DaemonHttpRunner $daemonRunner,
    CronHelper $cronHelper
  ) {
    $this->daemonRunner = $daemonRunner;
    $this->cronHelper = $cronHelper;
  }

  public function run($data) {
    $this->daemonRunner->run($data);
  }

  public function ping() {
     die(esc_html($this->cronHelper->pingDaemon()));
  }

  public function pingResponse() {
    $this->daemonRunner->ping();
  }
}
