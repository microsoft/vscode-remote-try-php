<?php declare(strict_types = 1);

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Mailer;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\WP\Notice as WPNotice;

class PendingApprovalNotice {

  const OPTION_NAME = 'mailpoet-pending-approval-notice';

  /** @var SettingsController */
  private $settings;

  public function __construct(
    SettingsController $settings
  ) {
    $this->settings = $settings;
  }

  public function init($shouldDisplay): ?string {
    // We should display the notice if the user is using MSS and the subscription is not approved
    if (
      $shouldDisplay
      && $this->settings->get('mta.method') === Mailer::METHOD_MAILPOET
      && $this->settings->get('mta.mailpoet_api_key_state')
      && $this->settings->get('mta.mailpoet_api_key_state.state', null) === Bridge::KEY_VALID
      && !$this->settings->get('mta.mailpoet_api_key_state.data.is_approved', false)
    ) {
      return $this->display();
    }

    return null;
  }

  public function getPendingApprovalTitle(): string {
    $message = __("MailPoet is [link]reviewing your subscription[/link].", 'mailpoet');
    return Helpers::replaceLinkTags(
      $message,
      'https://kb.mailpoet.com/article/379-our-approval-process',
      [
        'target' => '_blank',
        'rel' => 'noreferrer',
      ],
      'link'
    );
  }

  public function getPendingApprovalBody(): string {
    // translators: %s is the email subject, which will always be in English
    $message = sprintf(__("You can use all MailPoet features and send [link1]email previews[/link1] to your [link2]authorized email addresses[/link2], but sending to your email list contacts is temporarily paused until we review your subscription. If you don't hear from us within 48 hours, please check the inbox and spam folders of your MailPoet account email for follow-up emails with the subject \"%s\" and reply, or [link3]contact us[/link3].", 'mailpoet'), 'Your MailPoet Subscription Review');
    $message = Helpers::replaceLinkTags(
      $message,
      'https://kb.mailpoet.com/article/290-check-your-newsletter-before-sending-it',
      [
        'target' => '_blank',
        'rel' => 'noreferrer',
      ],
      'link1'
    );
    $message = Helpers::replaceLinkTags(
      $message,
      'https://kb.mailpoet.com/article/266-how-to-add-an-authorized-email-address-as-the-from-address#how-to-authorize-an-email-address',
      [
        'target' => '_blank',
        'rel' => 'noreferrer',
      ],
      'link2'
    );
    $message = Helpers::replaceLinkTags(
      $message,
      'https://www.mailpoet.com/support/',
      [
        'target' => '_blank',
        'rel' => 'noreferrer',
      ],
      'link3'
    );

    return $message;
  }

  public function getPendingApprovalMessage(): string {
    return sprintf('%s %s', $this->getPendingApprovalTitle(), $this->getPendingApprovalBody());
  }

  private function display(): string {
    $message = $this->getPendingApprovalMessage();
    WPNotice::displayWarning($message, '', self::OPTION_NAME);
    return $message;
  }
}
