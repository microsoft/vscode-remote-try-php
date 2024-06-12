<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Statistics\StatisticsOpensRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoetVendor\Carbon\Carbon;

class SubscribersEngagementScore extends SimpleWorker {
  const AUTOMATIC_SCHEDULING = true;
  const BATCH_SIZE = 60;
  const TASK_TYPE = 'subscribers_engagement_score';

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var StatisticsOpensRepository */
  private $statisticsOpensRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    SegmentsRepository $segmentsRepository,
    StatisticsOpensRepository $statisticsOpensRepository,
    SubscribersRepository $subscribersRepository
  ) {
    parent::__construct();
    $this->segmentsRepository = $segmentsRepository;
    $this->statisticsOpensRepository = $statisticsOpensRepository;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $recalculatedSubscribersCount = $this->recalculateSubscribers();
    if ($recalculatedSubscribersCount > 0) {
      $this->scheduleImmediately();
      return true;
    }

    $recalculatedSegmentsCount = $this->recalculateSegments();
    if ($recalculatedSegmentsCount > 0) {
      $this->scheduleImmediately();
      return true;
    }

    $this->schedule();
    return true;
  }

  private function recalculateSubscribers(): int {
    $subscribers = $this->subscribersRepository->findByUpdatedScoreNotInLastMonth(self::BATCH_SIZE);
    foreach ($subscribers as $subscriber) {
      $this->statisticsOpensRepository->recalculateSubscriberScore($subscriber);
    }
    return count($subscribers);
  }

  private function recalculateSegments(): int {
    $segments = $this->segmentsRepository->findByUpdatedScoreNotInLastDay(self::BATCH_SIZE);
    foreach ($segments as $segment) {
      $this->statisticsOpensRepository->recalculateSegmentScore($segment);
    }
    return count($segments);
  }

  public function getNextRunDate() {
    // random day of the next week
    $date = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $date->addDay();
    $date->setTime(mt_rand(0, 23), mt_rand(0, 59));
    return $date;
  }
}
