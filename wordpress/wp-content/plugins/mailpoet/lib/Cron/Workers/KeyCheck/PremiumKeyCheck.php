<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\KeyCheck;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronWorkerScheduler;
use MailPoet\InvalidStateException;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;

class PremiumKeyCheck extends KeyCheckWorker {
  const TASK_TYPE = 'premium_key_check';

  /** @var SettingsController */
  private $settings;

  public function __construct(
    SettingsController $settings,
    CronWorkerScheduler $cronWorkerScheduler
  ) {
    $this->settings = $settings;
    parent::__construct($cronWorkerScheduler);
  }

  public function checkProcessingRequirements() {
    return Bridge::isPremiumKeySpecified();
  }

  public function checkKey() {
    // for phpstan because we set bridge property in the init function
    if (!$this->bridge) {
      throw new InvalidStateException('The class was not initialized properly. Please call the Init method before.');
    };

    $premiumKey = $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    $result = $this->bridge->checkPremiumKey($premiumKey);
    $this->bridge->storePremiumKeyAndState($premiumKey, $result);
    return $result;
  }
}
