<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EventListeners\EmojiEncodingListener;
use MailPoet\Doctrine\EventListeners\LastSubscribedAtListener;
use MailPoet\Doctrine\EventListeners\NewsletterListener;
use MailPoet\Doctrine\EventListeners\SubscriberListener;
use MailPoet\Doctrine\EventListeners\TimestampListener;
use MailPoet\Doctrine\EventListeners\ValidationListener;
use MailPoet\Tracy\DoctrinePanel\DoctrinePanel;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\Configuration;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Events;
use Tracy\Debugger;

class EntityManagerFactory {

  /** @var Connection */
  private $connection;

  /** @var Configuration */
  private $configuration;

  /** @var TimestampListener */
  private $timestampListener;

  /** @var ValidationListener */
  private $validationListener;

  /** @var EmojiEncodingListener */
  private $emojiEncodingListener;

  /** @var LastSubscribedAtListener */
  private $lastSubscribedAtListener;

  /** @var SubscriberListener */
  private $subscriberListener;

  private NewsletterListener $newsletterListener;

  public function __construct(
    Connection $connection,
    Configuration $configuration,
    TimestampListener $timestampListener,
    ValidationListener $validationListener,
    EmojiEncodingListener $emojiEncodingListener,
    LastSubscribedAtListener $lastSubscribedAtListener,
    NewsletterListener $newsletterListener,
    SubscriberListener $subscriberListener
  ) {
    $this->connection = $connection;
    $this->configuration = $configuration;
    $this->timestampListener = $timestampListener;
    $this->validationListener = $validationListener;
    $this->emojiEncodingListener = $emojiEncodingListener;
    $this->lastSubscribedAtListener = $lastSubscribedAtListener;
    $this->subscriberListener = $subscriberListener;
    $this->newsletterListener = $newsletterListener;
  }

  public function createEntityManager(): EntityManager {
    $entityManager = EntityManager::create($this->connection, $this->configuration);
    $this->cleanupListeners($entityManager);
    $this->setupListeners($entityManager);
    if (
      class_exists(Debugger::class)
      && class_exists(DoctrinePanel::class)
    ) {
      DoctrinePanel::init($entityManager);
    }
    return $entityManager;
  }

  /**
   * We sometimes work with more EntityManager in tests, and the behavior could be inconsistent with multiple listeners
   */
  private function cleanupListeners(EntityManager $entityManager) {
    $eventManager = $entityManager->getEventManager();
    foreach ($eventManager->getListeners() as $event => $listeners) {
      if (!is_array($listeners)) {
        $eventManager->removeEventListener($event, $listeners);
        continue;
      }
      foreach ($listeners as $listener) {
        $eventManager->removeEventListener($event, $listener);
      }
    }

    $entityManager->getConfiguration()->getEntityListenerResolver()->clear(SubscriberListener::class);
  }

  private function setupListeners(EntityManager $entityManager) {
    $entityManager->getEventManager()->addEventListener(
      [Events::prePersist, Events::preUpdate],
      $this->timestampListener
    );

    $entityManager->getEventManager()->addEventListener(
      [Events::onFlush],
      $this->validationListener
    );

    $entityManager->getEventManager()->addEventListener(
      [Events::prePersist, Events::preUpdate],
      $this->emojiEncodingListener
    );

    $entityManager->getEventManager()->addEventListener(
      [Events::prePersist, Events::preUpdate],
      $this->lastSubscribedAtListener
    );

    $entityManager->getEventManager()->addEventListener(
      [Events::preUpdate],
      $this->newsletterListener
    );

    $entityManager->getConfiguration()->getEntityListenerResolver()->register($this->subscriberListener);
  }
}
