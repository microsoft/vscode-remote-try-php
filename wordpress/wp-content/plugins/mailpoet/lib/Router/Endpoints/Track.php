<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Router\Endpoints;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Newsletter\Links\Links;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Statistics\Track\Clicks;
use MailPoet\Statistics\Track\Opens;
use MailPoet\Subscribers\LinkTokens;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;

class Track {
  const ENDPOINT = 'track';
  const ACTION_CLICK = 'click';
  const ACTION_OPEN = 'open';
  public $allowedActions = [
    self::ACTION_CLICK,
    self::ACTION_OPEN,
  ];
  public $permissions = [
    'global' => AccessControl::NO_ACCESS_RESTRICTION,
  ];

  /** @var Clicks */
  private $clicks;

  /** @var Opens */
  private $opens;

  /** @var LinkTokens */
  private $linkTokens;

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  /** @var Links */
  private $links;

  public function __construct(
    Clicks $clicks,
    Opens $opens,
    SendingQueuesRepository $sendingQueuesRepository,
    SubscribersRepository $subscribersRepository,
    NewslettersRepository $newslettersRepository,
    NewsletterLinkRepository $newsletterLinkRepository,
    LinkTokens $linkTokens,
    Links $links
  ) {
    $this->clicks = $clicks;
    $this->opens = $opens;
    $this->linkTokens = $linkTokens;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
    $this->links = $links;
  }

  public function click($data) {
    return $this->clicks->track($this->_processTrackData($data));
  }

  public function open($data) {
    return $this->opens->track($this->_processTrackData($data));
  }

  public function _processTrackData($data) {
    $data = (object)$this->links->transformUrlDataObject($data);
    if (
      empty($data->queue_id) || // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      empty($data->subscriber_id) || // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      empty($data->subscriber_token) // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    ) {
      return false;
    }
    $data->queue = $this->sendingQueuesRepository->findOneById($data->queue_id);// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $data->subscriber = $this->subscribersRepository->findOneById($data->subscriber_id); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $data->newsletter = (isset($data->newsletter_id)) ? $this->newslettersRepository->findOneById($data->newsletter_id) : null; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    if (!$data->newsletter && ($data->queue instanceof SendingQueueEntity)) {
      $data->newsletter = $data->queue->getNewsletter();
    }
    if (!empty($data->link_hash)) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $data->link = $this->newsletterLinkRepository->findOneBy([
        'hash' => $data->link_hash, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'queue' => $data->queue_id, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      ]);
    }
    $data->userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : null;
    return $this->_validateTrackData($data);
  }

  public function _validateTrackData($data) {
    if (!$data->subscriber || !$data->queue || !$data->newsletter) return false;
    $subscriberTokenMatch = $this->linkTokens->verifyToken($data->subscriber, $data->subscriber_token); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    if (!$subscriberTokenMatch) {
      $this->terminate(403);
    }
    // return if this is a WP user previewing the newsletter
    if ($data->subscriber->isWPUser() && $data->preview) {
      return $data;
    }
    // check if the newsletter was sent to the subscriber
    return ($this->sendingQueuesRepository->isSubscriberProcessed($data->queue, $data->subscriber)) ?
      $data :
      false;
  }

  public function terminate($code) {
    WPFunctions::get()->statusHeader($code);
    WPFunctions::get()->getTemplatePart((string)$code);
    exit;
  }
}
