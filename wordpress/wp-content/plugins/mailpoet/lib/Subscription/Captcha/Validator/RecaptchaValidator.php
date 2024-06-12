<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription\Captcha\Validator;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\Subscription\Captcha\CaptchaConstants;
use MailPoet\WP\Functions as WPFunctions;

class RecaptchaValidator implements CaptchaValidator {


  /** @var SettingsController  */
  private $settings;

  /** @var WPFunctions  */
  private $wp;

  private const ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function validate(array $data): bool {

    $captchaSettings = $this->settings->get('captcha');
    if (empty($data['recaptchaResponseToken'])) {
      throw new ValidationError(__('Please check the CAPTCHA.', 'mailpoet'));
    }

    $secretToken = $captchaSettings['type'] === CaptchaConstants::TYPE_RECAPTCHA_INVISIBLE ? $captchaSettings['recaptcha_invisible_secret_token'] : $captchaSettings['recaptcha_secret_token'];

    $response = $this->wp->wpRemotePost(self::ENDPOINT, [
      'body' => [
        'secret' => $secretToken,
        'response' => $data['recaptchaResponseToken'],
      ],
    ]);
    if ($this->wp->isWpError($response)) {
      throw new ValidationError(__('Error while validating the CAPTCHA.', 'mailpoet'));
    }
    /** @var \stdClass $response */
    $response = json_decode($this->wp->wpRemoteRetrieveBody($response));
    if (empty($response->success)) {
      throw new ValidationError(__('Error while validating the CAPTCHA.', 'mailpoet'));
    }

    return true;
  }
}
