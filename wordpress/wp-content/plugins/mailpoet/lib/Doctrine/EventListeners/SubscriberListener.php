<?php declare(strict_types = 1);

namespace MailPoet\Doctrine\EventListeners;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\SubscriberChangesNotifier;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Doctrine\ORM\Event\LifecycleEventArgs;

class SubscriberListener {

  /** @var SubscriberChangesNotifier */
  private $subscriberChangesNotifier;

  public function __construct(
    SubscriberChangesNotifier $subscriberChangesNotifier
  ) {
    $this->subscriberChangesNotifier = $subscriberChangesNotifier;
  }

  private function maybeNotifyStatusChanged(SubscriberEntity $subscriber, LifecycleEventArgs $event): void {
    $entityManager = $event->getEntityManager();
    $unitOfWork = $entityManager->getUnitOfWork();
    $changeset = $unitOfWork->getEntityChangeSet($subscriber);

    if (array_key_exists('status', $changeset) && $changeset['status'][0] !== $changeset['status'][1]) {
      $this->subscriberChangesNotifier->subscriberStatusChanged((int)$subscriber->getId());
    }
  }

  public function postPersist(SubscriberEntity $subscriber, LifecycleEventArgs $event): void {
    $this->subscriberChangesNotifier->subscriberCreated((int)$subscriber->getId());
  }

  public function postUpdate(SubscriberEntity $subscriber, LifecycleEventArgs $event): void {
    $this->subscriberChangesNotifier->subscriberUpdated((int)$subscriber->getId());
    $this->maybeNotifyStatusChanged($subscriber, $event);
  }

  public function postRemove(SubscriberEntity $subscriber, LifecycleEventArgs $event): void {
    $this->subscriberChangesNotifier->subscriberDeleted((int)$subscriber->getId());
  }
}
