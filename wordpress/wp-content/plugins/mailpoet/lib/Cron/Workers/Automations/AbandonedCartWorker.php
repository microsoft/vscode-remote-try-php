<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Cron\Workers\SimpleWorker;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\WP\Functions as WPFunctions;

class AbandonedCartWorker extends SimpleWorker {
    const TASK_TYPE = 'automation_abandoned_cart';

    const ACTION = 'abandoned_cart';

    const AUTOMATIC_SCHEDULING = false;
    const BATCH_SIZE = 1000;

    private $automationStorage;

  public function __construct(
    AutomationStorage $automationStorage,
    WPFunctions $wp = null
  ) {
    parent::__construct($wp);
    $this->automationStorage = $automationStorage;
  }

  public function checkProcessingRequirements() {
        return true;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $productIds = $task->getMeta()['product_ids'] ?? [];
    $automationId = $task->getMeta()['automation_id'] ?? 0;
    $automationVersion = $task->getMeta()['automation_version'] ?? 0;

    if (!$productIds || !$automationId || !$automationVersion) {
      return true;
    }

    $lastActivityAt = $task->getCreatedAt();

    $subscribers = $task->getSubscribers();
    if ($subscribers->count() !== 1) {
      return false;
    }
    $subscriber = isset($subscribers[0]) ? $subscribers[0]->getSubscriber() : null;
    if (!$subscriber) {
      return false;
    }

    $automation = $this->automationStorage->getAutomation((int)$automationId, (int)$automationVersion);
    if (!$automation || $automation->getStatus() !== Automation::STATUS_ACTIVE) {
      return false;
    }

    $this->wp->doAction(
      self::ACTION,
      $subscriber,
      $productIds,
      $lastActivityAt
    );
    return true;
  }
}
