<?php declare(strict_types = 1);

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class SubscriberChangesNotifier {

  /** @var array<int, int> */
  private $createdSubscriberIds = [];

  /** @var array<int, int> */
  private $deletedSubscriberIds = [];

  /** @var array<int, int> */
  private $updatedSubscriberIds = [];

  /** @var array<int, int> */
  private $statusChangedSubscriberIds = [];

  /** @var array<int, int> */
  private $createdSubscriberBatches = [];

  /** @var array<int, int> */
  private $updatedSubscriberBatches = [];

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function notify() {
    $this->notifyCreations();
    $this->notifyUpdates();
    $this->notifyDeletes();
  }

  private function notifyCreations(): void {
    if (count($this->createdSubscriberIds) > 1) {
      $minTimestamp = min($this->createdSubscriberIds);
      if ($minTimestamp) {
        $this->createdSubscriberBatches[] = $minTimestamp;
        $this->createdSubscriberIds = []; // reset created subscribers
      }
    }

    foreach ($this->createdSubscriberIds as $subscriberId => $updatedAt) {
      $this->wp->doAction(SubscriberEntity::HOOK_SUBSCRIBER_CREATED, $subscriberId);
    }

    if ($this->createdSubscriberBatches) {
      $minTimestamp = min($this->createdSubscriberBatches);
      if ($minTimestamp) {
        $this->wp->doAction(SubscriberEntity::HOOK_MULTIPLE_SUBSCRIBERS_CREATED, $minTimestamp);
      }
    }
  }

  private function notifyUpdates(): void {
    // unset updated subscribers if subscriber is created
    foreach ($this->createdSubscriberIds as $subscriberId => $timestamp) {
      unset($this->updatedSubscriberIds[$subscriberId]);
      unset($this->statusChangedSubscriberIds[$subscriberId]);
    }

    if (count($this->updatedSubscriberIds) > 1) {
      $minTimestamp = min($this->updatedSubscriberIds);
      if ($minTimestamp) {
        $this->updatedSubscriberBatches[] = $minTimestamp;
        $this->updatedSubscriberIds = []; // reset updated subscribers
        $this->statusChangedSubscriberIds = []; // reset status changed subscribers
      }
    }

    foreach ($this->updatedSubscriberIds as $subscriberId => $updatedAt) {
      $this->wp->doAction(SubscriberEntity::HOOK_SUBSCRIBER_UPDATED, $subscriberId);
    }

    foreach ($this->statusChangedSubscriberIds as $subscriberId => $updatedAt) {
      $this->wp->doAction(SubscriberEntity::HOOK_SUBSCRIBER_STATUS_CHANGED, $subscriberId);
    }

    if ($this->updatedSubscriberBatches) {
      $minTimestamp = min($this->updatedSubscriberBatches);
      if ($minTimestamp) {
        $this->wp->doAction(SubscriberEntity::HOOK_MULTIPLE_SUBSCRIBERS_UPDATED, $minTimestamp);
      }
    }
  }

  private function notifyDeletes(): void {
    if (count($this->deletedSubscriberIds) === 1) {
      foreach ($this->deletedSubscriberIds as $subscriberId => $updatedAt) {
        $this->wp->doAction(SubscriberEntity::HOOK_SUBSCRIBER_DELETED, $subscriberId);
      }
    } elseif ($this->deletedSubscriberIds) {
      $this->wp->doAction(SubscriberEntity::HOOK_MULTIPLE_SUBSCRIBERS_DELETED, array_keys($this->deletedSubscriberIds));
    }
  }

  public function subscriberCreated(int $subscriberId): void {
    // store id as a key and timestamp change as the value
    $this->createdSubscriberIds[$subscriberId] = $this->getTimestamp();
  }

  public function subscriberUpdated(int $subscriberId): void {
    // store id as a key and timestamp change as the value
    $this->updatedSubscriberIds[$subscriberId] = $this->getTimestamp();
  }

  public function subscriberStatusChanged(int $subscriberId): void {
    // store id as a key and timestamp change as the value
    $this->statusChangedSubscriberIds[$subscriberId] = $this->getTimestamp();
  }

  public function subscriberDeleted(int $subscriberId): void {
    // store id as a key and timestamp change as the value
    $this->deletedSubscriberIds[$subscriberId] = $this->getTimestamp();
  }

  public function subscribersCreated(array $subscriberIds): void {
    foreach ($subscriberIds as $subscriberId) {
      $this->subscriberCreated((int)$subscriberId);
    }
  }

  public function subscribersUpdated(array $subscriberIds): void {
    foreach ($subscriberIds as $subscriberId) {
      $this->subscriberUpdated((int)$subscriberId);
    }
  }

  public function subscribersDeleted(array $subscriberIds): void {
    foreach ($subscriberIds as $subscriberId) {
      $this->subscriberDeleted((int)$subscriberId);
    }
  }

  public function subscribersBatchCreate(): void {
    $this->createdSubscriberBatches[] = $this->getTimestamp();
  }

  public function subscribersBatchUpdate(): void {
    $this->updatedSubscriberBatches[] = $this->getTimestamp();
  }

  private function getTimestamp(): int {
    $dateTime = Carbon::createFromTimestamp($this->wp->currentTime('timestamp', true), 'UTC');
    return $dateTime->getTimestamp();
  }
}
