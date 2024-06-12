<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Router\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Subscription as UserSubscription;
use MailPoet\Util\Request;
use MailPoet\WP\Functions as WPFunctions;

class Subscription {
  const ENDPOINT = 'subscription';
  const ACTION_CAPTCHA = 'captcha';
  const ACTION_CAPTCHA_IMAGE = 'captchaImage';
  const ACTION_CAPTCHA_AUDIO = 'captchaAudio';
  const ACTION_CONFIRM = 'confirm';
  const ACTION_MANAGE = 'manage';
  const ACTION_UNSUBSCRIBE = 'unsubscribe';
  const ACTION_CONFIRM_UNSUBSCRIBE = 'confirmUnsubscribe';
  const ACTION_RE_ENGAGEMENT = 'reEngagement';

  public $allowedActions = [
    self::ACTION_CAPTCHA,
    self::ACTION_CAPTCHA_IMAGE,
    self::ACTION_CAPTCHA_AUDIO,
    self::ACTION_CONFIRM,
    self::ACTION_MANAGE,
    self::ACTION_UNSUBSCRIBE,
    self::ACTION_CONFIRM_UNSUBSCRIBE,
    self::ACTION_RE_ENGAGEMENT,
  ];

  public $permissions = [
    'global' => AccessControl::NO_ACCESS_RESTRICTION,
  ];

  /** @var UserSubscription\Pages */
  private $subscriptionPages;

  /** @var WPFunctions */
  private $wp;

  /** @var UserSubscription\Captcha\CaptchaRenderer */
  private $captchaRenderer;

  /*** @var Request */
  private $request;

  public function __construct(
    UserSubscription\Pages $subscriptionPages,
    WPFunctions $wp,
    UserSubscription\Captcha\CaptchaRenderer $captchaRenderer,
    Request $request
  ) {
    $this->subscriptionPages = $subscriptionPages;
    $this->wp = $wp;
    $this->captchaRenderer = $captchaRenderer;
    $this->request = $request;
  }

  public function captcha($data) {
    $this->initSubscriptionPage(UserSubscription\Pages::ACTION_CAPTCHA, $data);
  }

  public function captchaImage($data) {
    $width = !empty($data['width']) ? (int)$data['width'] : null;
    $height = !empty($data['height']) ? (int)$data['height'] : null;
    $sessionId = !empty($data['captcha_session_id']) ? $data['captcha_session_id'] : null;
    return $this->captchaRenderer->renderImage($width, $height, $sessionId);
  }

  public function captchaAudio($data) {
    $sessionId = !empty($data['captcha_session_id']) ? $data['captcha_session_id'] : null;
    return $this->captchaRenderer->renderAudio($sessionId);
  }

  public function confirm($data) {
    $subscription = $this->initSubscriptionPage(UserSubscription\Pages::ACTION_CONFIRM, $data);
    $subscription->confirm();
  }

  public function confirmUnsubscribe($data) {
    $enableUnsubscribeConfirmation = $this->wp->applyFilters('mailpoet_unsubscribe_confirmation_enabled', true);
    if ($this->request->isPost()) {
      $this->applyOneClickUnsubscribeStrategy($data);
      exit;
    }

    if ($enableUnsubscribeConfirmation) {
      $this->initSubscriptionPage(UserSubscription\Pages::ACTION_CONFIRM_UNSUBSCRIBE, $data);
    } else {
      $this->unsubscribe($data);
    }
  }

  public function manage($data) {
    $this->initSubscriptionPage(UserSubscription\Pages::ACTION_MANAGE, $data);
  }

  public function unsubscribe($data) {
    if ($this->request->isPost()) {
      $this->applyOneClickUnsubscribeStrategy($data);
      exit;
    } else {
      $subscription = $this->initSubscriptionPage(UserSubscription\Pages::ACTION_UNSUBSCRIBE, $data);
      $subscription->unsubscribe(StatisticsUnsubscribeEntity::METHOD_LINK);
    }
  }

  public function reEngagement($data) {
    $this->initSubscriptionPage(UserSubscription\Pages::ACTION_RE_ENGAGEMENT, $data);
  }

  private function initSubscriptionPage($action, $data) {
    return $this->subscriptionPages->init($action, $data, true, true);
  }

  private function applyOneClickUnsubscribeStrategy($data): void {
    $subscription = $this->initSubscriptionPage(UserSubscription\Pages::ACTION_UNSUBSCRIBE, $data);
    $subscription->unsubscribe(StatisticsUnsubscribeEntity::METHOD_ONE_CLICK);
  }
}
