<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\EventListeners;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\Event\LifecycleEventArgs;

class LastSubscribedAtListener {
  /** @var Carbon */
  private $now;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->now = Carbon::createFromTimestamp($wp->currentTime('timestamp'));
  }

  public function prePersist(LifecycleEventArgs $eventArgs): void {
    $entity = $eventArgs->getEntity();

    if ($entity instanceof SubscriberEntity && $entity->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED) {
      $entity->setLastSubscribedAt($this->now->copy());
    }
  }

  public function preUpdate(LifecycleEventArgs $eventArgs): void {
    $entity = $eventArgs->getEntity();
    if (!$entity instanceof SubscriberEntity) {
      return;
    }

    $unitOfWork = $eventArgs->getEntityManager()->getUnitOfWork();
    $changeSet = $unitOfWork->getEntityChangeSet($entity);
    if (!isset($changeSet['status'])) {
      return;
    }

    [$oldStatus, $newStatus] = $changeSet['status'];
    // Update last_subscribed_at when status changes to subscribed
    if ($oldStatus !== SubscriberEntity::STATUS_SUBSCRIBED && $newStatus === SubscriberEntity::STATUS_SUBSCRIBED) {
      $entity->setLastSubscribedAt($this->now->copy());
    }
  }
}
