<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronHelper;
use MailPoet\Cron\CronWorkerInterface;
use MailPoet\Cron\CronWorkerRunner;
use MailPoet\Cron\CronWorkerScheduler;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

abstract class SimpleWorker implements CronWorkerInterface {
  const TASK_TYPE = null;
  const AUTOMATIC_SCHEDULING = true;
  const SUPPORT_MULTIPLE_INSTANCES = true;

  public $timer;

  /** @var CronHelper */
  protected $cronHelper;

  /** @var CronWorkerScheduler */
  protected $cronWorkerScheduler;

  /** @var WPFunctions */
  protected $wp;

  /** @var ScheduledTasksRepository */
  protected $scheduledTasksRepository;

  public function __construct(
    WPFunctions $wp = null
  ) {
    if (static::TASK_TYPE === null) {
      throw new \Exception('Constant TASK_TYPE is not defined on subclass ' . get_class($this));
    }

    if ($wp === null) $wp = ContainerWrapper::getInstance()->get(WPFunctions::class);
    $this->wp = $wp;
    $this->cronHelper = ContainerWrapper::getInstance()->get(CronHelper::class);
    $this->cronWorkerScheduler = ContainerWrapper::getInstance()->get(CronWorkerScheduler::class);
    $this->scheduledTasksRepository = ContainerWrapper::getInstance()->get(ScheduledTasksRepository::class);
  }

  public function getTaskType() {
    return static::TASK_TYPE;
  }

  public function supportsMultipleInstances() {
    return static::SUPPORT_MULTIPLE_INSTANCES;
  }

  public function schedule() {
    $this->cronWorkerScheduler->schedule(static::TASK_TYPE, $this->getNextRunDate());
  }

  protected function scheduleImmediately(): void {
    $this->cronWorkerScheduler->schedule(static::TASK_TYPE, $this->getNextRunDateImmediately());
  }

  public function checkProcessingRequirements() {
    return true;
  }

  public function init() {
  }

  public function prepareTaskStrategy(ScheduledTaskEntity $task, $timer) {
    return true;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    return true;
  }

  public function getNextRunDate() {
    // random day of the next week
    $date = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $date->setISODate((int)$date->format('o'), ((int)$date->format('W')) + 1, mt_rand(1, 7));
    $date->startOfDay();
    return $date;
  }

  protected function getNextRunDateImmediately(): Carbon {
    return Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
  }

  public function scheduleAutomatically() {
    return static::AUTOMATIC_SCHEDULING;
  }

  protected function getCompletedTasks() {
    return $this->scheduledTasksRepository->findCompletedByType(static::TASK_TYPE, CronWorkerRunner::TASK_BATCH_SIZE);
  }
}
