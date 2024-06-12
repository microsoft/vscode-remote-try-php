<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EventListeners;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\WP\Emoji;
use MailPoetVendor\Doctrine\ORM\Event\LifecycleEventArgs;

class EmojiEncodingListener {
  /** @var Emoji */
  private $emoji;

  public function __construct(
    Emoji $emoji
  ) {
    $this->emoji = $emoji;
  }

  public function prePersist(LifecycleEventArgs $eventArgs) {
    $this->sanitizeEmojiBeforeSaving($eventArgs);
  }

  public function preUpdate(LifecycleEventArgs $eventArgs) {
    $this->sanitizeEmojiBeforeSaving($eventArgs);
  }

  private function sanitizeEmojiBeforeSaving(LifecycleEventArgs $eventArgs) {
    $entity = $eventArgs->getEntity();
    if ($entity instanceof FormEntity) {
      $body = $entity->getBody();
      if ($body !== null) {
        $entity->setBody($this->emoji->sanitizeEmojisInFormBody($body));
      }
    }
  }
}
