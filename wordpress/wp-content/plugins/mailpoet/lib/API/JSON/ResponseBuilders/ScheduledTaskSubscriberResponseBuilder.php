<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskSubscriberEntity;

class ScheduledTaskSubscriberResponseBuilder {
  public function build(ScheduledTaskSubscriberEntity $scheduledSubscriber) {
    $subscriber = $scheduledSubscriber->getSubscriber();
    $task = $scheduledSubscriber->getTask();
    return [
      'processed' => $scheduledSubscriber->getProcessed(),
      'failed' => $scheduledSubscriber->getFailed(),
      'error' => $scheduledSubscriber->getError(),
      'taskId' => $task ? $task->getId() : null,
      'email' => $subscriber ? $subscriber->getEmail() : null,
      'subscriberId' => $subscriber ? $subscriber->getId() : null,
      'firstName' => $subscriber ? $subscriber->getFirstName() : null,
      'lastName' => $subscriber ? $subscriber->getLastName() : null,
    ];
  }

  public function buildForListing(array $scheduledSubscribers) {
    $data = [];
    foreach ($scheduledSubscribers as $scheduledSubscriber) {
      $data[] = $this->build($scheduledSubscriber);
    }
    return $data;
  }
}
