<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronHelper;
use MailPoet\Router\Endpoints\CronDaemon;
use MailPoet\Settings\SettingsController;

class WooSystemInfo {


  private $cronHelper;

  /** @var SettingsController  */
  private $settings;

  public function __construct(
    CronHelper $cronHelper,
    SettingsController $settings
  ) {
    $this->cronHelper = $cronHelper;
    $this->settings = $settings;
  }

  public function sendingMethod(): string {
    return $this->settings->get('mta.method');
  }

  public function transactionalEmails(): string {
    return $this->settings->get('send_transactional_emails') ?
      __('Current sending method', 'mailpoet') :
      __('Default WordPress sending method', 'mailpoet');

  }

  public function taskSchedulerMethod(): string {
    return $this->settings->get('cron_trigger.method');
  }

  public function cronPingUrl(): string {
    return $this->cronHelper->getCronUrl(CronDaemon::ACTION_PING);
  }

  public function toArray(): array {
    return [
      'sending_method' => $this->sendingMethod(),
      'transactional_emails' => $this->transactionalEmails(),
      'task_scheduler_method' => $this->taskSchedulerMethod(),
      'cron_ping_url' => $this->cronPingUrl(),
    ];
  }
}
