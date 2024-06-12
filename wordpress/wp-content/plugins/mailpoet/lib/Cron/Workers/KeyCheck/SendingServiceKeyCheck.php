<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\KeyCheck;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\ServicesChecker;
use MailPoet\Cron\CronWorkerScheduler;
use MailPoet\InvalidStateException;
use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\MailerLog;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoetVendor\Carbon\Carbon;

class SendingServiceKeyCheck extends KeyCheckWorker {
  const TASK_TYPE = 'sending_service_key_check';

  /** @var SettingsController */
  private $settings;

  /** @var ServicesChecker */
  private $servicesChecker;

  public function __construct(
    SettingsController $settings,
    ServicesChecker $servicesChecker,
    CronWorkerScheduler $cronWorkerScheduler
  ) {
    $this->settings = $settings;
    $this->servicesChecker = $servicesChecker;
    parent::__construct($cronWorkerScheduler);
  }

  public function checkProcessingRequirements() {
    return Bridge::isMPSendingServiceEnabled();
  }

  /**
   * @return \DateTimeInterface|Carbon
   */
  public function getNextRunDate() {
    // when key pending approval, check key sate every hour
    if ($this->servicesChecker->isMailPoetAPIKeyPendingApproval()) {
      $date = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
      return $date->addHour();
    }
    return parent::getNextRunDate();
  }

  public function checkKey() {
    // for phpstan because we set bridge property in the init function
    if (!$this->bridge) {
      throw new InvalidStateException('The class was not initialized properly. Please call the Init method before.');
    };

    $wasPendingApproval = $this->servicesChecker->isMailPoetAPIKeyPendingApproval();

    $mssKey = $this->settings->get(Mailer::MAILER_CONFIG_SETTING_NAME)['mailpoet_api_key'];
    $result = $this->bridge->checkMSSKey($mssKey);
    $this->bridge->storeMSSKeyAndState($mssKey, $result);

    $isPendingApproval = $this->servicesChecker->isMailPoetAPIKeyPendingApproval();
    if ($wasPendingApproval && !$isPendingApproval) {
      MailerLog::resumeSending();
    }
    return $result;
  }
}
