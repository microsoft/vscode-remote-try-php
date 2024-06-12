<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\ViewInBrowser;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Links\Links;
use MailPoet\Newsletter\Renderer\Renderer;
use MailPoet\Newsletter\Shortcodes\Shortcodes;
use MailPoet\Settings\TrackingConfig;
use MailPoet\WP\Emoji;

class ViewInBrowserRenderer {
  /** @var Emoji */
  private $emoji;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var Renderer */
  private $renderer;

  /** @var Shortcodes */
  private $shortcodes;

  /** @var Links */
  private $links;

  public function __construct(
    Emoji $emoji,
    TrackingConfig $trackingConfig,
    Shortcodes $shortcodes,
    Renderer $renderer,
    Links $links
  ) {
    $this->emoji = $emoji;
    $this->trackingConfig = $trackingConfig;
    $this->renderer = $renderer;
    $this->shortcodes = $shortcodes;
    $this->links = $links;
  }

  public function render(
    bool $isPreview,
    NewsletterEntity $newsletter,
    SubscriberEntity $subscriber = null,
    SendingQueueEntity $queue = null
  ) {
    $wpUserPreview = $isPreview;
    $isTrackingEnabled = $this->trackingConfig->isEmailTrackingEnabled();

    if ($queue && $queue->getNewsletterRenderedBody()) {
      $body = $queue->getNewsletterRenderedBody();
      if (is_array($body)) {
        $newsletterBody = $body['html'];
      } else {
        $newsletterBody = '';
      }
      $newsletterBody = $this->emoji->decodeEmojisInBody($newsletterBody);
      // rendered newsletter body has shortcodes converted to links; we need to
      // isolate "view in browser", "unsubscribe" and "manage subscription" links
      // and convert them to shortcodes, which later will be replaced with "#" when
      // newsletter is previewed
      if ($wpUserPreview && preg_match($this->links->getLinkRegex(), $newsletterBody)) {
        $newsletterBody = $this->links->convertHashedLinksToShortcodesAndUrls(
          $newsletterBody,
          $queue->getId(),
          $convertAll = true
        );
        // remove open tracking link
        $newsletterBody = str_replace(Links::DATA_TAG_OPEN, '', $newsletterBody);
      }
    } else {
      if ($wpUserPreview) {
        $newsletterBody = $this->renderer->renderAsPreview($newsletter, 'html');
      } else {
        $newsletterBody = $this->renderer->render($newsletter, $sendingTask = null, 'html');
      }
    }
    $this->prepareShortcodes(
      $newsletter,
      $subscriber,
      $queue,
      $wpUserPreview
    );
    $renderedNewsletter = $this->shortcodes->replace($newsletterBody);
    if (!$wpUserPreview && $queue && $subscriber && $isTrackingEnabled) {
      $renderedNewsletter = $this->links->replaceSubscriberData(
        $subscriber->getId(),
        $queue->getId(),
        $renderedNewsletter
      );
    }
    return $renderedNewsletter;
  }

  private function prepareShortcodes(
    NewsletterEntity $newsletter,
    ?SubscriberEntity $subscriber,
    ?SendingQueueEntity $queue,
    bool $wpUserPreview
  ) {
    $this->shortcodes->setQueue($queue);
    $this->shortcodes->setNewsletter($newsletter);
    $this->shortcodes->setWpUserPreview($wpUserPreview);
    $this->shortcodes->setSubscriber($subscriber);
  }
}
