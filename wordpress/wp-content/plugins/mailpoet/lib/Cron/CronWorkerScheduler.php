<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class CronWorkerScheduler {
  /** @var WPFunctions */
  private $wp;

  /** @var ScheduledTasksRepository */
  private $scheduledTaskRepository;

  public function __construct(
    WPFunctions $wp,
    ScheduledTasksRepository $scheduledTaskRepository
  ) {
    $this->wp = $wp;
    $this->scheduledTaskRepository = $scheduledTaskRepository;
  }

  public function scheduleImmediatelyIfNotRunning($taskType, $priority = ScheduledTaskEntity::PRIORITY_LOW): ScheduledTaskEntity {
    $task = $this->scheduledTaskRepository->findScheduledOrRunningTask($taskType);
    // Do nothing when task is running
    if (($task instanceof ScheduledTaskEntity) && $task->getStatus() === null) {
      return $task;
    }
    $now = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    // Reschedule existing scheduled task
    if ($task instanceof ScheduledTaskEntity) {
      $task->setScheduledAt($now);
      $task->setPriority($priority);
      $this->scheduledTaskRepository->flush();
    }
    // Schedule new task
    return $this->schedule($taskType, $now, $priority);
  }

  public function schedule($taskType, $nextRunDate, $priority = ScheduledTaskEntity::PRIORITY_LOW): ScheduledTaskEntity {
    $alreadyScheduled = $this->scheduledTaskRepository->findScheduledTask($taskType);
    if ($alreadyScheduled) {
      return $alreadyScheduled;
    }
    $task = new ScheduledTaskEntity();
    $task->setType($taskType);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $task->setPriority($priority);
    $task->setScheduledAt($nextRunDate);
    $this->scheduledTaskRepository->persist($task);
    $this->scheduledTaskRepository->flush();
    return $task;
  }

  public function reschedule(ScheduledTaskEntity $task, $timeout) {
    $scheduledAt = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $task->setScheduledAt($scheduledAt->addMinutes($timeout));
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $this->scheduledTaskRepository->persist($task);
    $this->scheduledTaskRepository->flush();
  }

  public function rescheduleProgressively(ScheduledTaskEntity $task): int {
    $scheduledAt = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $rescheduleCount = $task->getRescheduleCount();
    $timeout = (int)min(ScheduledTaskEntity::BASIC_RESCHEDULE_TIMEOUT * pow(2, $rescheduleCount), ScheduledTaskEntity::MAX_RESCHEDULE_TIMEOUT);
    $task->setScheduledAt($scheduledAt->addMinutes($timeout));
    $task->setRescheduleCount($rescheduleCount + 1);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $this->scheduledTaskRepository->persist($task);
    $this->scheduledTaskRepository->flush();

    return $timeout;
  }
}
