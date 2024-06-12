<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue\Tasks;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Links\Links as NewsletterLinks;
use MailPoet\Router\Endpoints\Track;
use MailPoet\Router\Router;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Subscribers\LinkTokens;
use MailPoet\Subscription\SubscriptionUrlFactory;
use MailPoet\Util\Helpers;

class Links {
  /** @var LinkTokens */
  private $linkTokens;

  /** @var NewsletterLinks */
  private $newsletterLinks;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    LinkTokens $linkTokens,
    NewsletterLinks $newsletterLinks,
    NewsletterLinkRepository $newsletterLinkRepository,
    TrackingConfig $trackingConfig
  ) {
    $this->linkTokens = $linkTokens;
    $this->newsletterLinks = $newsletterLinks;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
    $this->trackingConfig = $trackingConfig;
  }

  public function process($renderedNewsletter, NewsletterEntity $newsletter, SendingQueueEntity $queue) {
    [$renderedNewsletter, $links] = $this->hashAndReplaceLinks($renderedNewsletter, $newsletter->getId(), $queue->getId());
    $this->saveLinks($links, $newsletter, $queue);
    return $renderedNewsletter;
  }

  public function hashAndReplaceLinks($renderedNewsletter, $newsletterId, $queueId) {
    // join HTML and TEXT rendered body into a text string
    $content = Helpers::joinObject($renderedNewsletter);
    [$content, $links] = $this->newsletterLinks->process($content, $newsletterId, $queueId);
    $links = $this->newsletterLinks->ensureInstantUnsubscribeLink($links);
    // split the processed body with hashed links back to HTML and TEXT
    list($renderedNewsletter['html'], $renderedNewsletter['text'])
      = Helpers::splitObject($content);
    return [
      $renderedNewsletter,
      $links,
    ];
  }

  public function saveLinks($links, NewsletterEntity $newsletter, SendingQueueEntity $queue) {
    return $this->newsletterLinks->save($links, $newsletter->getId(), $queue->getId());
  }

  public function getUnsubscribeUrl($queueId, SubscriberEntity $subscriber = null) {
    if ($this->trackingConfig->isEmailTrackingEnabled() && $subscriber) {
      $linkHash = $this->newsletterLinkRepository->findOneBy(
        [
          'queue' => $queueId,
          'url' => NewsletterLinkEntity::INSTANT_UNSUBSCRIBE_LINK_SHORT_CODE,
        ]
      );

      if (!$linkHash instanceof NewsletterLinkEntity) {
        return '';
      }
      $data = $this->newsletterLinks->createUrlDataObject(
        $subscriber->getId(),
        $this->linkTokens->getToken($subscriber),
        $queueId,
        $linkHash->getHash(),
        false
      );
      $url = Router::buildRequest(
        Track::ENDPOINT,
        Track::ACTION_CLICK,
        $data
      );
    } else {
      $subscriptionUrlFactory = SubscriptionUrlFactory::getInstance();
      $url = $subscriptionUrlFactory->getUnsubscribeUrl($subscriber, $queueId);
    }
    return $url;
  }

  public function getOneClickUnsubscribeUrl($queueId, SubscriberEntity $subscriber): string {
    $subscriptionUrlFactory = SubscriptionUrlFactory::getInstance();
    return $subscriptionUrlFactory->getUnsubscribeUrl($subscriber, $queueId);
  }
}
