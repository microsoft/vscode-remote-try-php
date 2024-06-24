<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class UnauthorizedEmailNotice {

  const OPTION_NAME = 'unauthorized-email-addresses-notice';

  /** @var SettingsController|null */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settings = null
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function init($shouldDisplay) {
    if (!$this->settings instanceof SettingsController) {
      throw new \Exception('This method can only be called if SettingsController is provided');
    }
    $validationError = $this->settings->get(AuthorizedEmailsController::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING);
    if ($shouldDisplay && isset($validationError['invalid_sender_address'])) {
      return $this->display($validationError);
    }
  }

  public function display($validationError) {
    $message = $this->getMessage($validationError);
    $extraClasses = 'mailpoet-js-error-unauthorized-emails-notice';
    Notice::displayError($message, $extraClasses, self::OPTION_NAME, false, false);
  }

  public function getMessage($validationError) {
    $message = $this->getMessageText($validationError);
    $message .= sprintf(
      '<p>%s &nbsp; %s &nbsp; %s</p>',
      $this->getAuthorizeEmailButton($validationError),
      $this->getDifferentEmailButton(),
      $this->getResumeSendingButton($validationError)
    );
    return $message;
  }

  private function getMessageText($validationError) {
    // translators: %s is the email address.
    $text = _x(
      '<b>Sending all of your emails has been paused</b> because your email address <b>%s</b> hasn’t been authorized yet.',
      'Email addresses have to be authorized to be used to send emails. %s will be replaced by an email address.',
      'mailpoet'
    );
    $message = str_replace('%s', EscapeHelper::escapeHtmlText($validationError['invalid_sender_address']), $text);
    return "<p>$message</p>";
  }

  private function getAuthorizeEmailButton($validationError) {
    $email = $this->wp->escAttr($validationError['invalid_sender_address']);
    $button = '<a target="_blank" href="https://account.mailpoet.com/authorization?email=' . $email . '" class="button button-primary mailpoet-js-button-authorize-email-and-sender-domain" data-type="email" data-email="' . $email . '">' . __('Authorize this email address', 'mailpoet') . '</a>';
    return $button;
  }

  private function getDifferentEmailButton() {
    $button = '<button class="button button-secondary mailpoet-js-button-fix-this">' . __('Use a different email address', 'mailpoet') . '</button>';
    return $button;
  }

  private function getResumeSendingButton($validationError) {
    $email = $this->wp->escAttr($validationError['invalid_sender_address']);
    $button = '<button class="button button-secondary mailpoet-js-button-resume-sending" value="' . $email . '">' . __('Resume sending', 'mailpoet') . '</button>';
    return $button;
  }
}
