<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Newsletter\Scheduler\ReEngagementScheduler;
use MailPoetVendor\Carbon\Carbon;

class ReEngagementEmailsScheduler extends SimpleWorker {
  const TASK_TYPE = 'schedule_re_engagement_email';

  /** @var ReEngagementScheduler */
  private $reEngagementEmailsScheduler;

  public function __construct(
    ReEngagementScheduler $reEngagementEmailsScheduler
  ) {
    parent::__construct();
    $this->reEngagementEmailsScheduler = $reEngagementEmailsScheduler;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $this->reEngagementEmailsScheduler->scheduleAll();
    return true;
  }

  public function getNextRunDate() {
    return Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))->addDay();
  }
}
