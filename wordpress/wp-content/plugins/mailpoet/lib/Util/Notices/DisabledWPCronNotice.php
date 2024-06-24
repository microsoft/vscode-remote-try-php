<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronTrigger;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class DisabledWPCronNotice {

  const DISMISS_NOTICE_TIMEOUT_SECONDS = YEAR_IN_SECONDS;
  const OPTION_NAME = 'dismissed-wp-cron-disabled-notice';

  /** @var WPFunctions */
  private $wp;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settings
  ) {
    $this->wp = $wp;
    $this->settings = $settings;
  }

  public function init($shouldDisplay) {
    if (!$shouldDisplay) {
      return null;
    }
    $isDismissed = $this->wp->getTransient(self::OPTION_NAME);
    $currentMethod = $this->settings->get(CronTrigger::SETTING_CURRENT_METHOD);
    $isWPCronMethodActive = $currentMethod === CronTrigger::METHOD_ACTION_SCHEDULER;
    if (!$isDismissed && $isWPCronMethodActive && $this->isWPCronDisabled()) {
      return $this->display();
    }
  }

  public function isWPCronDisabled() {
    return defined('DISABLE_WP_CRON') && DISABLE_WP_CRON;
  }

  public function display() {
    $errorString = __('WordPress built-in cron is disabled with the DISABLE_WP_CRON constant on your website, this prevents MailPoet sending from working. Please enable WordPress built-in cron or choose a different cron method in MailPoet Settings.', 'mailpoet');

    $buttonString = __('[link]Go to Settings[/link]', 'mailpoet');
    $error = $errorString . '<br><br>' . Helpers::replaceLinkTags($buttonString, 'admin.php?page=mailpoet-settings#advanced', [
      'class' => 'button-primary',
    ]);

    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    return Notice::displayError($error, $extraClasses, self::OPTION_NAME, true, false);
  }

  public function disable() {
    $this->wp->setTransient(self::OPTION_NAME, true, self::DISMISS_NOTICE_TIMEOUT_SECONDS);
  }
}
