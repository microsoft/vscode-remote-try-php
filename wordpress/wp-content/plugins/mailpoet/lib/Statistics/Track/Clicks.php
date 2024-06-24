<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\UserAgentEntity;
use MailPoet\Newsletter\Shortcodes\Categories\Link as LinkShortcodeCategory;
use MailPoet\Newsletter\Shortcodes\Shortcodes;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Statistics\StatisticsClicksRepository;
use MailPoet\Statistics\UserAgentsRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Cookies;
use MailPoet\WP\Functions as WPFunctions;

class Clicks {

  const REVENUE_TRACKING_COOKIE_NAME = 'mailpoet_revenue_tracking';
  const REVENUE_TRACKING_COOKIE_EXPIRY = 60 * 60 * 24 * 14;

  /** @var Cookies */
  private $cookies;

  /** @var SubscriberCookie */
  private $subscriberCookie;

  /** @var Shortcodes */
  private $shortcodes;

  /** @var LinkShortcodeCategory */
  private $linkShortcodeCategory;

  /** @var Opens */
  private $opens;

  /** @var StatisticsClicksRepository */
  private $statisticsClicksRepository;

  /** @var UserAgentsRepository */
  private $userAgentsRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    Cookies $cookies,
    SubscriberCookie $subscriberCookie,
    Shortcodes $shortcodes,
    Opens $opens,
    StatisticsClicksRepository $statisticsClicksRepository,
    UserAgentsRepository $userAgentsRepository,
    LinkShortcodeCategory $linkShortcodeCategory,
    SubscribersRepository $subscribersRepository,
    TrackingConfig $trackingConfig
  ) {
    $this->cookies = $cookies;
    $this->subscriberCookie = $subscriberCookie;
    $this->shortcodes = $shortcodes;
    $this->linkShortcodeCategory = $linkShortcodeCategory;
    $this->opens = $opens;
    $this->statisticsClicksRepository = $statisticsClicksRepository;
    $this->userAgentsRepository = $userAgentsRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->trackingConfig = $trackingConfig;
  }

  /**
   * @param \stdClass|null $data
   */
  public function track($data) {
    if (!$data || empty($data->link)) {
      return $this->abort();
    }
    /** @var SubscriberEntity $subscriber */
    $subscriber = $data->subscriber;
    /** @var SendingQueueEntity $queue */
    $queue = $data->queue;
    /** @var NewsletterEntity $newsletter */
    $newsletter = $data->newsletter;
    /** @var NewsletterLinkEntity $link */
    $link = $data->link;
    $wpUserPreview = ($data->preview && ($subscriber->isWPUser()));
    // log statistics only if the action did not come from
    // a WP user previewing the newsletter
    if (!$wpUserPreview) {
      $userAgent = !empty($data->userAgent) ? $this->userAgentsRepository->findOrCreate($data->userAgent) : null;
      $statisticsClicks = $this->statisticsClicksRepository->createOrUpdateClickCount(
        $link,
        $subscriber,
        $newsletter,
        $queue,
        $userAgent
      );
      if (
        $userAgent instanceof UserAgentEntity &&
        ($userAgent->getUserAgentType() === UserAgentEntity::USER_AGENT_TYPE_HUMAN
        || $statisticsClicks->getUserAgentType() === UserAgentEntity::USER_AGENT_TYPE_MACHINE)
      ) {
        $statisticsClicks->setUserAgent($userAgent);
        $statisticsClicks->setUserAgentType($userAgent->getUserAgentType());
      }
      $this->statisticsClicksRepository->flush();
      $this->sendRevenueCookie($statisticsClicks);

      $subscriberId = $subscriber->getId();
      if ($subscriberId) {
        $this->subscriberCookie->setSubscriberId($subscriberId);
      }

      // track open event
      $this->opens->track($data, $displayImage = false);
      // Update engagement date
      $this->subscribersRepository->maybeUpdateLastClickAt($subscriber);
    }
    $url = $this->processUrl($link->getUrl(), $newsletter, $subscriber, $queue, $wpUserPreview);
    do_action('mailpoet_link_clicked', $link, $subscriber, $wpUserPreview);
    $this->redirectToUrl($url);
  }

  private function sendRevenueCookie(StatisticsClickEntity $clicks) {
    if ($this->trackingConfig->isCookieTrackingEnabled()) {
      $this->cookies->set(
        self::REVENUE_TRACKING_COOKIE_NAME,
        [
          'statistics_clicks' => $clicks->getId(),
          'created_at' => time(),
        ],
        [
          'expires' => time() + self::REVENUE_TRACKING_COOKIE_EXPIRY,
          'path' => '/',
        ]
      );
    }
  }

  public function processUrl(
    string $url,
    NewsletterEntity $newsletter,
    SubscriberEntity $subscriber,
    SendingQueueEntity $queue,
    bool $wpUserPreview
  ) {
    if (preg_match('/\[link:(?P<action>.*?)\]/', $url, $shortcode)) {
      if (!$shortcode['action']) $this->abort();
      $url = $this->linkShortcodeCategory->processShortcodeAction(
        $shortcode['action'],
        $newsletter,
        $subscriber,
        $queue,
        $wpUserPreview
      );
    } else {
      $this->shortcodes->setQueue($queue);
      $this->shortcodes->setNewsletter($newsletter);
      $this->shortcodes->setSubscriber($subscriber);
      $this->shortcodes->setWpUserPreview($wpUserPreview);
      $url = $this->shortcodes->replace($url);
    }
    return $url;
  }

  public function abort() {
    global $wp_query;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    WPFunctions::get()->statusHeader(404);
    $wp_query->set_404();// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    WPFunctions::get()->getTemplatePart((string)404);
    exit;
  }

  public function redirectToUrl($url) {
    header('Location: ' . $url, true, 302);
    exit;
  }
}
