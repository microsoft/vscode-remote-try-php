<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue\Tasks;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Shortcodes\Shortcodes as NewsletterShortcodes;

class Shortcodes {
  /**
   * @param string $content
   * @param string|null $contentSource
   * @param NewsletterEntity|null $newsletter
   * @param SubscriberEntity|null $subscriber
   * @param SendingQueueEntity|null $queue
   */
  public static function process($content, $contentSource = null, NewsletterEntity $newsletter = null, SubscriberEntity $subscriber = null, SendingQueueEntity $queue = null) {
    /** @var NewsletterShortcodes $shortcodes */
    $shortcodes = ContainerWrapper::getInstance()->get(NewsletterShortcodes::class);

    if ($queue instanceof SendingQueueEntity) {
      $shortcodes->setQueue($queue);
    } else {
      $shortcodes->setQueue(null);
    }

    if ($newsletter instanceof NewsletterEntity) {
      $shortcodes->setNewsletter($newsletter);
    } else {
      $shortcodes->setNewsletter(null);
    }

    if ($subscriber instanceof SubscriberEntity) {
      $shortcodes->setSubscriber($subscriber);
    } else {
      $shortcodes->setSubscriber(null);
    }
    return $shortcodes->replace($content, $contentSource);
  }
}
