<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;

interface CronWorkerInterface {
  /** @return string */
  public function getTaskType();

  /** @return bool */
  public function scheduleAutomatically();

  /** @return bool */
  public function supportsMultipleInstances();

  /** @return bool */
  public function checkProcessingRequirements();

  public function init();

  /**
   * @param ScheduledTaskEntity $task
   * @param float $timer
   * @return bool
   */
  public function prepareTaskStrategy(ScheduledTaskEntity $task, $timer);

  /**
   * @param ScheduledTaskEntity $task
   * @param float $timer
   * @return bool
   */
  public function processTaskStrategy(ScheduledTaskEntity $task, $timer);

  /** @return \DateTimeInterface */
  public function getNextRunDate();
}
