<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription\Captcha\Validator;

if (!defined('ABSPATH')) exit;


use MailPoet\Subscribers\SubscriberIPsRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Subscription\Captcha\CaptchaPhrase;
use MailPoet\Subscription\Captcha\CaptchaSession;
use MailPoet\Subscription\SubscriptionUrlFactory;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;

class BuiltInCaptchaValidator implements CaptchaValidator {


  /** @var SubscriptionUrlFactory  */
  private $subscriptionUrlFactory;

  /** @var CaptchaPhrase  */
  private $captchaPhrase;

  /** @var CaptchaSession  */
  private $captchaSession;

  /** @var WPFunctions  */
  private $wp;

  /** @var SubscriberIPsRepository  */
  private $subscriberIPsRepository;

  /** @var SubscribersRepository  */
  private $subscribersRepository;

  public function __construct(
    SubscriptionUrlFactory $subscriptionUrlFactory,
    CaptchaPhrase $captchaPhrase,
    CaptchaSession $captchaSession,
    WPFunctions $wp,
    SubscriberIPsRepository $subscriberIPsRepository,
    SubscribersRepository $subscribersRepository
  ) {
    $this->subscriptionUrlFactory = $subscriptionUrlFactory;
    $this->captchaPhrase = $captchaPhrase;
    $this->captchaSession = $captchaSession;
    $this->wp = $wp;
    $this->subscriberIPsRepository = $subscriberIPsRepository;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function validate(array $data): bool {
    $isBuiltinCaptchaRequired = $this->isRequired(isset($data['email']) ? $data['email'] : null);
    if (!$isBuiltinCaptchaRequired) {
      return true;
    }
    if (empty($data['captcha'])) {
      throw new ValidationError(
        __('Please fill in the CAPTCHA.', 'mailpoet'),
        [
          'redirect_url' => $this->subscriptionUrlFactory->getCaptchaUrl($this->captchaSession->getId()),
        ]
      );
    }
    $captchaHash = $this->captchaPhrase->getPhrase();
    if (empty($captchaHash)) {
      throw new ValidationError(
        __('Please regenerate the CAPTCHA.', 'mailpoet'),
        [
          'redirect_url' => $this->subscriptionUrlFactory->getCaptchaUrl($this->captchaSession->getId()),
        ]
      );
    }

    if (!hash_equals(strtolower($data['captcha']), strtolower($captchaHash))) {
      $this->captchaPhrase->resetPhrase();
      throw new ValidationError(
        __('The characters entered do not match with the previous CAPTCHA.', 'mailpoet'),
        [
          'refresh_captcha' => true,
        ]
      );
    }

    return true;

  }

  public function isRequired($subscriberEmail = null) {
    if ($this->isUserExemptFromCaptcha()) {
      return false;
    }

    $subscriptionCaptchaRecipientLimit = $this->wp->applyFilters('mailpoet_subscription_captcha_recipient_limit', 0);
    if ($subscriptionCaptchaRecipientLimit === 0) {
      return true;
    }

    // Check limits per recipient if enabled
    if ($subscriberEmail) {
      $subscriber = $this->subscribersRepository->findOneBy(['email' => $subscriberEmail]);
      if (
        $subscriber && $subscriber->getConfirmationsCount() >= $subscriptionCaptchaRecipientLimit
      ) {
        return true;
      }
    }

    // Check limits per IP address
    /** @var int|string $subscriptionCaptchaWindow */
    $subscriptionCaptchaWindow = $this->wp->applyFilters('mailpoet_subscription_captcha_window', MONTH_IN_SECONDS);

    $subscriberIp = Helpers::getIP();

    if (empty($subscriberIp)) {
      return false;
    }

    $subscriptionCount = $this->subscriberIPsRepository->getCountByIPAndCreatedAtAfterTimeInSeconds(
      $subscriberIp,
      (int)$subscriptionCaptchaWindow
    );

    if ($subscriptionCount > 0) {
      return true;
    }

    return false;
  }

  private function isUserExemptFromCaptcha() {
    if (!$this->wp->isUserLoggedIn()) {
      return false;
    }
    $user = $this->wp->wpGetCurrentUser();
    $roles = $this->wp->applyFilters('mailpoet_subscription_captcha_exclude_roles', ['administrator', 'editor']);
    return !empty(array_intersect((array)$roles, $user->roles));
  }
}
