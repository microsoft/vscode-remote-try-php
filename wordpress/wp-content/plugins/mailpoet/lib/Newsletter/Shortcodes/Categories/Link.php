<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Shortcodes\Categories;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Url as NewsletterUrl;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Subscription\SubscriptionUrlFactory;
use MailPoet\WP\Functions as WPFunctions;

class Link implements CategoryInterface {
  const CATEGORY_NAME = 'link';

  /** @var SettingsController */
  private $settings;

  /** @var NewsletterUrl */
  private $newsletterUrl;

  /** @var WPFunctions */
  private $wp;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    SettingsController $settings,
    NewsletterUrl $newsletterUrl,
    WPFunctions $wp,
    TrackingConfig $trackingConfig
  ) {
    $this->settings = $settings;
    $this->newsletterUrl = $newsletterUrl;
    $this->wp = $wp;
    $this->trackingConfig = $trackingConfig;
  }

  public function process(
    array $shortcodeDetails,
    NewsletterEntity $newsletter = null,
    SubscriberEntity $subscriber = null,
    SendingQueueEntity $queue = null,
    string $content = '',
    bool $wpUserPreview = false
  ): ?string {
    $subscriptionUrlFactory = SubscriptionUrlFactory::getInstance();

    switch ($shortcodeDetails['action']) {
      case 'subscription_unsubscribe_url':
        return self::processUrl(
          $shortcodeDetails['action'],
          $subscriptionUrlFactory->getConfirmUnsubscribeUrl(
            $wpUserPreview ? null : $subscriber,
            $queue ? $queue->getId() : null
          ),
          $queue,
          $wpUserPreview
        );

      case 'subscription_instant_unsubscribe_url':
        return self::processUrl(
          $shortcodeDetails['action'],
          $subscriptionUrlFactory->getUnsubscribeUrl(
            $wpUserPreview ? null : $subscriber,
            $queue ? $queue->getId() : null
          ),
          $queue,
          $wpUserPreview
        );

      case 'subscription_manage_url':
        return self::processUrl(
          $shortcodeDetails['action'],
          $subscriptionUrlFactory->getManageUrl($wpUserPreview ? null : $subscriber),
          $queue,
          $wpUserPreview
        );

      case 'newsletter_view_in_browser_url':
        $url = $this->newsletterUrl->getViewInBrowserUrl(
          $newsletter,
          $wpUserPreview ? null : $subscriber,
          $queue,
          $wpUserPreview
        );
        return self::processUrl($shortcodeDetails['action'], $url, $queue, $wpUserPreview);

      case 'subscription_re_engage_url':
        $url = $subscriptionUrlFactory->getReEngagementUrl($wpUserPreview ? null : $subscriber);
        return self::processUrl($shortcodeDetails['action'], $url, $queue, $wpUserPreview);

      default:
        $shortcode = self::getFullShortcode($shortcodeDetails['action']);
        $url = $this->wp->applyFilters(
          'mailpoet_newsletter_shortcode_link',
          $shortcode,
          $newsletter,
          $subscriber,
          $queue,
          $shortcodeDetails['arguments'],
          $wpUserPreview
        );

        return ($url !== $shortcode) ?
          self::processUrl($shortcodeDetails['action'], $url, $queue, $wpUserPreview) :
          null;
    }
  }

  public function processUrl($action, $url, ?SendingQueueEntity $queue, $wpUserPreview = false): string {
    if ($wpUserPreview) return $url;
    return ($queue && $this->trackingConfig->isEmailTrackingEnabled()) ?
      self::getFullShortcode($action) :
      $url;
  }

  public function processShortcodeAction(
    $shortcodeAction,
    NewsletterEntity $newsletter = null,
    SubscriberEntity $subscriber = null,
    SendingQueueEntity $queue = null,
    $wpUserPreview = false
  ): ?string {
    $subscriptionUrlFactory = SubscriptionUrlFactory::getInstance();
    switch ($shortcodeAction) {
      case 'subscription_unsubscribe_url':
        $url = $subscriptionUrlFactory->getConfirmUnsubscribeUrl(
          $subscriber,
          $queue ? $queue->getId() : null
        );
        break;
      case 'subscription_instant_unsubscribe_url':
        $url = $subscriptionUrlFactory->getUnsubscribeUrl(
          $subscriber,
          $queue ? $queue->getId() : null
        );
        break;
      case 'subscription_manage_url':
        $url = $subscriptionUrlFactory->getManageUrl($subscriber);
        break;
      case 'newsletter_view_in_browser_url':
        $url = $this->newsletterUrl->getViewInBrowserUrl(
          $newsletter,
          $subscriber,
          $queue,
          false
        );
        break;
      case 'subscription_re_engage_url':
        $url = $subscriptionUrlFactory->getReEngagementUrl($subscriber);
        break;
      default:
        $shortcode = self::getFullShortcode($shortcodeAction);
        $url = $this->wp->applyFilters(
          'mailpoet_newsletter_shortcode_link',
          $shortcode,
          $newsletter,
          $subscriber,
          $queue,
          $wpUserPreview
        );
        $url = ($url !== $shortcodeAction) ? $url : null;
        break;
    }
    return $url;
  }

  private function getFullShortcode($action): string {
    return sprintf('[link:%s]', $action);
  }
}
