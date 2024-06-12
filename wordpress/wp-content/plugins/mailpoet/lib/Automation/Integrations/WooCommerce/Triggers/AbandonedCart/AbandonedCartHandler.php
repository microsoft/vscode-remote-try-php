<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers\AbandonedCart;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Cron\Workers\Automations\AbandonedCartWorker;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository;
use MailPoetVendor\Carbon\Carbon;

class AbandonedCartHandler {

  const TASK_ABANDONED_CART = 'automation_abandoned_cart';

  /** @var WordPress */
  private $wp;

  /** @var ScheduledTasksRepository  */
  private $tasksRepository;

  /** @var ScheduledTaskSubscribersRepository */
  private $taskSubscribersRepository;

  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    WordPress $wp,
    ScheduledTasksRepository $tasksRepository,
    ScheduledTaskSubscribersRepository $taskSubscribersRepository,
    AutomationStorage $automationStorage
  ) {
    $this->wp = $wp;
    $this->tasksRepository = $tasksRepository;
    $this->taskSubscribersRepository = $taskSubscribersRepository;
    $this->automationStorage = $automationStorage;
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      AbandonedCart::HOOK_SCHEDULE,
      [
        $this,
        'schedule',
      ],
      10,
      2
    );
    $this->wp->addAction(
      AbandonedCart::HOOK_RE_SCHEDULE,
      [
        $this,
        'reschedule',
      ]
    );
    $this->wp->addAction(
      AbandonedCart::HOOK_CANCEL,
      [
        $this,
        'cancel',
      ]
    );
  }

  /**
   * @param SubscriberEntity $subscriber
   * @param int[] $productIds
   * @return void
   */
  public function schedule(SubscriberEntity $subscriber, array $productIds) {

    $abandonedCartAutomations = $this->automationStorage->getActiveAutomationsByTriggerKey(AbandonedCartTrigger::KEY);
    $this->cancel($subscriber);
    array_map(
      function (Automation $automation) use ($subscriber, $productIds) {
        $this->scheduleForSingleAutomation($subscriber, $productIds, $automation);
      },
      $abandonedCartAutomations
    );
  }

  /**
   * @param SubscriberEntity $subscriber
   * @param int[] $productIds
   * @param Automation $automation
   * @return void
   * @throws InvalidStateException
   */
  private function scheduleForSingleAutomation(SubscriberEntity $subscriber, array $productIds, Automation $automation) {
    $trigger = $automation->getTrigger(AbandonedCartTrigger::KEY);
    if (!$trigger) {
      throw new InvalidStateException(sprintf('Abandoned cart trigger is missing from automation %d', $automation->getId()));
    }

    $wait = $trigger->getArgs()['wait'] * 60;
    $scheduledAt = Carbon::createFromTimestamp((int)$this->wp->currentTime('timestamp') + $wait);
    $task = new ScheduledTaskEntity();
    $task->setType(AbandonedCartWorker::TASK_TYPE);

    $lastActivity = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $task->setCreatedAt($lastActivity);
    $task->setPriority(ScheduledTaskEntity::PRIORITY_MEDIUM);
    $task->setMeta([
      'product_ids' => $productIds,
      'automation_id' => $automation->getId(),
      'automation_version' => $automation->getVersionId(),
    ]);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $task->setScheduledAt($scheduledAt);
    $this->tasksRepository->persist($task);
    $this->tasksRepository->flush();

    $taskSubscriber = new ScheduledTaskSubscriberEntity($task, $subscriber);
    $task->getSubscribers()->add($taskSubscriber);
    $this->taskSubscribersRepository->persist($taskSubscriber);
    $this->taskSubscribersRepository->flush();
  }

  public function reschedule(SubscriberEntity $subscriber): void {
    $tasks = $this->tasksRepository->findByTypeAndSubscriber(AbandonedCartWorker::TASK_TYPE, $subscriber);
    if (!$tasks) {
      return;
    }
    $this->cancel($subscriber);

    foreach ($tasks as $task) {
      $meta = $task->getMeta();
      $automation = isset($meta['automation_id']) ? $this->automationStorage->getAutomation((int)$meta['automation_id']) : null;
      if (!$automation) {
        continue;
      }
      $this->scheduleForSingleAutomation($subscriber, $meta['product_ids'] ?? [], $automation);
    }

  }

  public function cancel(SubscriberEntity $subscriber): void {
    $existingTasks = $this->tasksRepository->findByTypeAndSubscriber(AbandonedCartWorker::TASK_TYPE, $subscriber);
    if (!$existingTasks) {
      return;
    }

    foreach ($existingTasks as $task) {
      if ($task->getStatus() !== ScheduledTaskEntity::STATUS_SCHEDULED) {
        continue;
      }
      foreach ($task->getSubscribers() as $taskSubscriber) {
        $this->taskSubscribersRepository->remove($taskSubscriber);
      }
      $this->tasksRepository->remove($task);
    }

    $this->tasksRepository->flush();
  }
}
