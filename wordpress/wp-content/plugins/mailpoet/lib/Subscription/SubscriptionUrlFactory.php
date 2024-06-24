<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Router\Endpoints\Subscription as SubscriptionEndpoint;
use MailPoet\Router\Router;
use MailPoet\Settings\Pages as SettingsPages;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\LinkTokens;
use MailPoet\WP\Functions as WPFunctions;

class SubscriptionUrlFactory {

  /** @var SubscriptionUrlFactory */
  private static $instance;

  /** @var WPFunctions */
  private $wp;

  /** @var SettingsController */
  private $settings;

  /** @var LinkTokens */
  private $linkTokens;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settings,
    LinkTokens $linkTokens
  ) {
    $this->wp = $wp;
    $this->settings = $settings;
    $this->linkTokens = $linkTokens;
  }

  public function getCaptchaUrl($sessionId) {
    $post = $this->getPost($this->settings->get('subscription.pages.captcha'));
    return $this->getSubscriptionUrl($post, 'captcha', null, ['captcha_session_id' => $sessionId]);
  }

  public function getCaptchaImageUrl($width, $height, $sessionId) {
    $post = $this->getPost($this->settings->get('subscription.pages.captcha'));
    return $this->getSubscriptionUrl(
      $post,
      'captchaImage',
      null,
      ['width' => $width, 'height' => $height, 'captcha_session_id' => $sessionId]
    );
  }

  public function getCaptchaAudioUrl($sessionId) {
    $post = $this->getPost($this->settings->get('subscription.pages.captcha'));
    $url = $this->getSubscriptionUrl(
      $post,
      'captchaAudio',
      null,
      [
        'cacheBust' => time(),
        'captcha_session_id' => $sessionId,
      ]
    );
    return $url;
  }

  public function getConfirmationUrl(SubscriberEntity $subscriber = null) {
    $post = $this->getPost($this->settings->get('subscription.pages.confirmation'));
    return $this->getSubscriptionUrl($post, 'confirm', $subscriber);
  }

  public function getConfirmUnsubscribeUrl(SubscriberEntity $subscriber = null, int $queueId = null) {
    $post = $this->getPost($this->settings->get('subscription.pages.confirm_unsubscribe'));
    $data = $queueId && $subscriber ? ['queueId' => $queueId] : null;
    return $this->getSubscriptionUrl($post, 'confirm_unsubscribe', $subscriber, $data);
  }

  public function getManageUrl(SubscriberEntity $subscriber = null) {
    $post = $this->getPost($this->settings->get('subscription.pages.manage'));
    return $this->getSubscriptionUrl($post, 'manage', $subscriber);
  }

  public function getUnsubscribeUrl(SubscriberEntity $subscriber = null, int $queueId = null) {
    $post = $this->getPost($this->settings->get('subscription.pages.unsubscribe'));
    $data = $queueId && $subscriber ? ['queueId' => $queueId] : null;
    return $this->getSubscriptionUrl($post, 'unsubscribe', $subscriber, $data);
  }

  public function getReEngagementUrl(SubscriberEntity $subscriber = null) {
    $reEngagementSetting = $this->settings->get('reEngagement');
    $postId = $reEngagementSetting['page'] ?? null;

    $post = $this->getPost($postId);
    return $this->getSubscriptionUrl($post, 're_engagement', $subscriber);
  }

  public function getSubscriptionUrl(
    $post = null,
    $action = null,
    SubscriberEntity $subscriber = null,
    $data = null
  ) {
    if ($post === null || $action === null) return;

    $url = $this->wp->getPermalink($post);
    if ($subscriber !== null) {
      $subscriberData = [
        'token' => $this->linkTokens->getToken($subscriber),
        'email' => $subscriber->getEmail(),
      ];
      $data = array_merge($data ?? [], $subscriberData);
    } elseif (is_null($data)) {
      $data = [
        'preview' => 1,
      ];
    }

    $params = [
      Router::NAME,
      'endpoint=' . SubscriptionEndpoint::ENDPOINT,
      'action=' . $action,
      'data=' . Router::encodeRequestData($data),
    ];

    // add parameters
    $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . join('&', $params);

    $urlParams = parse_url($url);
    if (!is_array($urlParams) || empty($urlParams['scheme'])) {
      $url = $this->wp->getBloginfo('url') . $url;
    }

    return $url;
  }

  /**
   * @return SubscriptionUrlFactory
   */
  public static function getInstance() {
    if (!self::$instance instanceof SubscriptionUrlFactory) {
      $linkTokens = ContainerWrapper::getInstance()->get(LinkTokens::class);
      self::$instance = new SubscriptionUrlFactory(new WPFunctions, SettingsController::getInstance(), $linkTokens);
    }
    return self::$instance;
  }

  private function getPost($post = null) {
    if ($post) {
      $postObject = $this->wp->getPost($post);
      if ($postObject) {
        return $postObject;
      }
    }
    // Resort to a default MailPoet page if no page is selected
    $pages = SettingsPages::getMailPoetPages();
    return reset($pages);
  }
}
