<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;

class SubscriberPayload implements Payload {
  /** @var SubscriberEntity */
  private $subscriber;

  public function __construct(
    SubscriberEntity $subscriber
  ) {
    $this->subscriber = $subscriber;
  }

  public function getId(): int {
    $id = $this->subscriber->getId();
    if (!$id) {
      throw new InvalidStateException();
    }
    return $id;
  }

  public function getEmail(): string {
    return $this->subscriber->getEmail();
  }

  public function getStatus(): string {
    return $this->subscriber->getStatus();
  }

  public function isWpUser(): bool {
    return $this->subscriber->isWPUser();
  }

  public function getWpUserId(): ?int {
    return $this->subscriber->getWpUserId();
  }

  public function getSubscriber(): SubscriberEntity {
    return $this->subscriber;
  }
}
