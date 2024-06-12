<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;

class AfterMigrationNotice {

  const OPTION_NAME = 'mailpoet_display_after_migration_notice';

  /** @var SettingsController */
  private $settings;

  public function __construct() {
    $this->settings = SettingsController::getInstance();
  }

  public function enable() {
    $this->settings->set(self::OPTION_NAME, true);
  }

  public function disable() {
    $this->settings->set(self::OPTION_NAME, false);
  }

  public function init($shouldDisplay) {
    if ($shouldDisplay && $this->settings->get(self::OPTION_NAME, false)) {
      return $this->display();
    }
  }

  private function display() {
    $message = Helpers::replaceLinkTags(
      __('Congrats! You’re progressing well so far. Complete your upgrade thanks to this [link]checklist[/link].', 'mailpoet'),
      'https://kb.mailpoet.com/article/199-checklist-after-migrating-to-mailpoet3',
      [
        'target' => '_blank',
      ]
    );

    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';
    $dataNoticeName = self::OPTION_NAME;

    \MailPoet\WP\Notice::displaySuccess($message, $extraClasses, $dataNoticeName);
    return $message;
  }
}
