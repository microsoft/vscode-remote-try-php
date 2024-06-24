<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\ViewInBrowser;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Newsletter\Url as NewsletterUrl;
use MailPoet\Subscribers\LinkTokens;
use MailPoet\Subscribers\SubscribersRepository;

class ViewInBrowserController {
  /** @var LinkTokens */
  private $linkTokens;

  /** @var NewsletterUrl */
  private $newsletterUrl;

  /** @var ViewInBrowserRenderer */
  private $viewInBrowserRenderer;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  public function __construct(
    LinkTokens $linkTokens,
    NewsletterUrl $newsletterUrl,
    NewslettersRepository $newslettersRepository,
    ViewInBrowserRenderer $viewInBrowserRenderer,
    SendingQueuesRepository $sendingQueuesRepository,
    SubscribersRepository $subscribersRepository
  ) {
    $this->linkTokens = $linkTokens;
    $this->viewInBrowserRenderer = $viewInBrowserRenderer;
    $this->subscribersRepository = $subscribersRepository;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->newsletterUrl = $newsletterUrl;
    $this->newslettersRepository = $newslettersRepository;
  }

  public function view(array $data) {
    $data = $this->newsletterUrl->transformUrlDataObject($data);
    $isPreview = !empty($data['preview']);
    $newsletter = $this->getNewsletter($data);
    $subscriber = $this->getSubscriber($data);

    // if queue and subscriber exist, subscriber must have received the newsletter
    $queue = isset($data['queue_id']) ? $this->sendingQueuesRepository->findOneById($data['queue_id']) : null;
    if (!$isPreview && $queue && $subscriber->getId() && !$this->sendingQueuesRepository->isSubscriberProcessed($queue, $subscriber)) {
      throw new \InvalidArgumentException("Subscriber did not receive the newsletter yet");
    }

    return $this->viewInBrowserRenderer->render($isPreview, $newsletter, $subscriber, $queue);
  }

  private function getNewsletter(array $data) {
    // newsletter - ID is mandatory, hash must be set and valid
    if (empty($data['newsletter_id'])) {
      throw new \InvalidArgumentException("Missing 'newsletter_id'");
    }
    if (empty($data['newsletter_hash'])) {
      throw new \InvalidArgumentException("Missing 'newsletter_hash'");
    }

    $newsletter = $this->newslettersRepository->findOneById($data['newsletter_id']);
    if (!$newsletter) {
      throw new \InvalidArgumentException("Invalid 'newsletter_id'");
    }

    if ($data['newsletter_hash'] !== $newsletter->getHash()) {
      throw new \InvalidArgumentException("Invalid 'newsletter_hash'");
    }
    return $newsletter;
  }

  private function getSubscriber(array $data): SubscriberEntity {
    // subscriber is optional; if exists, token must validate
    $subscriber = null;
    if (!empty($data['subscriber_id'])) {
      $subscriber = $this->subscribersRepository->findOneById($data['subscriber_id']);
    }
    if ($subscriber && empty($data['subscriber_token'])) {
      throw new \InvalidArgumentException("Missing 'subscriber_token'");
    }

    if ($subscriber && !$this->linkTokens->verifyToken($subscriber, $data['subscriber_token'])) {
      throw new \InvalidArgumentException("Invalid 'subscriber_token'");
    }

    // if this is a preview and subscriber does not exist,
    // attempt to set subscriber to the current logged-in WP user
    if (!$subscriber && !empty($data['preview'])) {
      $subscriber = $this->subscribersRepository->getCurrentWPUser();
    }

    return $subscriber ?? new SubscriberEntity();
  }
}
