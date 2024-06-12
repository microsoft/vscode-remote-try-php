<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Router\Endpoints\ViewInBrowser as ViewInBrowserEndpoint;
use MailPoet\Router\Router;
use MailPoet\Subscribers\LinkTokens;

class Url {
  /** @var LinkTokens */
  private $linkTokens;

  public function __construct(
    LinkTokens $linkTokens
  ) {
    $this->linkTokens = $linkTokens;
  }

  public function getViewInBrowserUrl(
    ?NewsletterEntity $newsletter,
    ?SubscriberEntity $subscriber = null,
    ?SendingQueueEntity $queue = null,
    bool $preview = true
  ) {
    $data = $this->createUrlDataObject($newsletter, $subscriber, $queue, $preview);
    return Router::buildRequest(
      ViewInBrowserEndpoint::ENDPOINT,
      ViewInBrowserEndpoint::ACTION_VIEW,
      $data
    );
  }

  public function createUrlDataObject(
    ?NewsletterEntity $newsletter,
    ?SubscriberEntity $subscriber,
    ?SendingQueueEntity $queue,
    bool $preview
  ) {
    $newsletterId = $newsletter && $newsletter->getId() ? $newsletter->getId() : 0;
    $newsletterHash = $newsletter && $newsletter->getHash() ? $newsletter->getHash() : 0;
    $sendingQueueId = $queue && $queue->getId() ? $queue->getId() : 0;

    return [
      $newsletterId,
      $newsletterHash,
      $subscriber && $subscriber->getId() ? $subscriber->getId() : 0,
      $subscriber && $subscriber->getId() ? $this->linkTokens->getToken($subscriber) : 0,
      $sendingQueueId,
      (int)$preview,
    ];
  }

  public function transformUrlDataObject($data) {
    reset($data);
    if (!is_int(key($data))) return $data;
    $transformedData = [];
    $transformedData['newsletter_id'] = (!empty($data[0])) ? $data[0] : false;
    $transformedData['newsletter_hash'] = (!empty($data[1])) ? $data[1] : false;
    $transformedData['subscriber_id'] = (!empty($data[2])) ? $data[2] : false;
    $transformedData['subscriber_token'] = (!empty($data[3])) ? $data[3] : false;
    $transformedData['queue_id'] = (!empty($data[4])) ? $data[4] : false;
    $transformedData['preview'] = (!empty($data[5])) ? $data[5] : false;
    return $transformedData;
  }
}
