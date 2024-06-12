<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Menu;
use MailPoet\Mailer\MailerError;
use MailPoet\Mailer\MailerLog;
use MailPoet\Newsletter\Renderer\EscapeHelper;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;

class UnauthorizedEmailInNewslettersNotice {

  const OPTION_NAME = 'unauthorized-email-in-newsletters-addresses-notice';

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function init($shouldDisplay) {
    $validationError = $this->settings->get(AuthorizedEmailsController::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING);
    if ($shouldDisplay && isset($validationError['invalid_senders_in_newsletters'])) {
      return $this->display($validationError);
    }
  }

  public function display($validationError) {
    $message = $this->getMessageText();
    $message .= $this->getNewslettersLinks($validationError);
    $message .= $this->getFixThisButton();
    // Use Mailer log errors display system to display this notice
    $mailerLog = MailerLog::setError(MailerLog::getMailerLog(), MailerError::OPERATION_AUTHORIZATION, $message);
    MailerLog::updateMailerLog($mailerLog);
  }

  private function getMessageText() {
    $message = __('<b>Your automatic emails have been paused</b> because some email addresses haven’t been authorized yet.', 'mailpoet');
    return "<p>$message</p>";
  }

  private function getNewslettersLinks($validationError) {
    $links = '';
    foreach ($validationError['invalid_senders_in_newsletters'] as $error) {
      // translators: %s is the newsletter subject.
      $linkText = _x('Update the from address of %s', '%s will be replaced by a newsletter subject', 'mailpoet');
      $linkText = str_replace('%s', EscapeHelper::escapeHtmlText($error['subject']), $linkText);
      $linkUrl = $this->wp->adminUrl('admin.php?page=' . Menu::EMAILS_PAGE_SLUG . '#/send/' . $error['newsletter_id']);
      $link = Helpers::replaceLinkTags("[link]{$linkText}[/link]", $linkUrl, ['target' => '_blank']);
      $links .= "<p>$link</p>";
    }
    return $links;
  }

  private function getFixThisButton() {
    $button = '<button class="button button-primary mailpoet-js-button-fix-this">' . __('Fix this!', 'mailpoet') . '</button>';
    return "<p>$button</p>";
  }
}
