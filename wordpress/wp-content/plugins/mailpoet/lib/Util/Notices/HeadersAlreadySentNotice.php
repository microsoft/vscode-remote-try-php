<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Subscription\Captcha\CaptchaConstants;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class HeadersAlreadySentNotice {

  const DISMISS_NOTICE_TIMEOUT_SECONDS = YEAR_IN_SECONDS;
  const OPTION_NAME = 'dismissed-headers-already-sent-notice';

  /** @var SettingsController */
  private $settings;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SettingsController $settings,
    TrackingConfig $trackingConfig,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->trackingConfig = $trackingConfig;
    $this->wp = $wp;
  }

  public function init($shouldDisplay) {
    if (!$shouldDisplay) {
      return null;
    }
    $captchaEnabled = $this->settings->get('captcha.type') === CaptchaConstants::TYPE_BUILTIN;
    $trackingEnabled = $this->trackingConfig->isEmailTrackingEnabled();
    if ($this->areHeadersAlreadySent()) {
      return $this->display($captchaEnabled, $trackingEnabled);
    }
  }

  public function areHeadersAlreadySent() {
    return !get_transient(self::OPTION_NAME)
      && ($this->headersSent() || $this->isWhitespaceInBuffer());
  }

  protected function headersSent() {
    return headers_sent();
  }

  public function isWhitespaceInBuffer() {
    $content = ob_get_contents();
    if (!$content) {
      return false;
    }
    return preg_match('/^\s+$/', $content);
  }

  public function display($captchaEnabled, $trackingEnabled) {
    if (!$captchaEnabled && !$trackingEnabled) {
      return null;
    }

    $errorString = __('It looks like there\'s an issue with some of the PHP files on your website which is preventing MailPoet from functioning correctly. If not resolved, you may experience:', 'mailpoet');
    $errorStringTracking = __('Inaccurate tracking of email opens and clicks', 'mailpoet');
    $errorStringCaptcha = __('CAPTCHA not rendering correctly', 'mailpoet');
    $errorString = $errorString . '<br>'
      . ($trackingEnabled ? ('<br> - ' . $errorStringTracking) : '')
      . ($captchaEnabled ? ('<br> - ' . $errorStringCaptcha) : '');

    $howToResolveString = __('[link]Learn how to fix this issue and restore functionality[/link]', 'mailpoet');
    $error = $errorString . '<br><br>' . Helpers::replaceLinkTags($howToResolveString, 'https://kb.mailpoet.com/article/325-the-captcha-image-doesnt-show-up', [
      'target' => '_blank',
      'class' => 'button-primary',
    ]);

    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    return Notice::displayError($error, $extraClasses, self::OPTION_NAME, true, false);
  }

  public function disable() {
    $this->wp->setTransient(self::OPTION_NAME, true, self::DISMISS_NOTICE_TIMEOUT_SECONDS);
  }
}
