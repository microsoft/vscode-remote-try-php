<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class InactiveSubscribersNotice {
  const OPTION_NAME = 'inactive-subscribers-notice';
  const MIN_INACTIVE_SUBSCRIBERS_COUNT = 50;

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SettingsController $settings,
    SubscribersRepository $subscribersRepository,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function init($shouldDisplay) {
    if (!$shouldDisplay || !$this->settings->get(self::OPTION_NAME, true)) {
      return;
    }

    // don't display notice if user has changed the default inactive time range
    $inactiveDays = (int)$this->settings->get('deactivate_subscriber_after_inactive_days');
    if ($inactiveDays !== SettingsController::DEFAULT_DEACTIVATE_SUBSCRIBER_AFTER_INACTIVE_DAYS) {
      return;
    }

    $inactiveSubscribersCount = $this->subscribersRepository->countBy(['deletedAt' => null, 'status' => SubscriberEntity::STATUS_INACTIVE]);
    if ($inactiveSubscribersCount < self::MIN_INACTIVE_SUBSCRIBERS_COUNT) {
      return;
    }
    return $this->display($inactiveSubscribersCount);
  }

  public function disable() {
    $this->settings->set(self::OPTION_NAME, false);
  }

  private function display($inactiveSubscribersCount) {
    $goToSettingsString = __('Go to the Advanced Settings', 'mailpoet');

    $notice = sprintf(
      // translators: %d is the number of inactive subscribers.
      __('Good news! MailPoet won’t send emails to your %s inactive subscribers. This is a standard practice to maintain good deliverability and open rates. But if you want to disable it, you can do so in settings. [link]Read more.[/link]', 'mailpoet'),
      $this->wp->numberFormatI18n($inactiveSubscribersCount)
    );
    $notice = Helpers::replaceLinkTags($notice, 'https://kb.mailpoet.com/article/264-inactive-subscribers', [
      'target' => '_blank',
    ]);
    $notice = "<p>$notice</p>";
    $notice .= '<p><a href="admin.php?page=mailpoet-settings#advanced" class="button button-primary">' . $goToSettingsString . '</a></p>';

    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    Notice::displaySuccess($notice, $extraClasses, self::OPTION_NAME, false);
    return $notice;
  }
}
