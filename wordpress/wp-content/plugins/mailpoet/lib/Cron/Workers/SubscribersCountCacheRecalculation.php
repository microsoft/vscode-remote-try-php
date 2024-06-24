<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Cache\TransientCache;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Subscribers\SubscribersCountsController;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class SubscribersCountCacheRecalculation extends SimpleWorker {
  private const EXPIRATION_IN_MINUTES = 30;
  const TASK_TYPE = 'subscribers_count_cache_recalculation';
  const AUTOMATIC_SCHEDULING = false;
  const SUPPORT_MULTIPLE_INSTANCES = false;

  /** @var TransientCache */
  private $transientCache;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  public function __construct(
    TransientCache $transientCache,
    SegmentsRepository $segmentsRepository,
    SubscribersCountsController $subscribersCountsController,
    WPFunctions $wp
  ) {
    parent::__construct($wp);
    $this->transientCache = $transientCache;
    $this->segmentsRepository = $segmentsRepository;
    $this->subscribersCountsController = $subscribersCountsController;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $segments = $this->segmentsRepository->findAll();
    foreach ($segments as $segment) {
      $this->recalculateSegmentCache($timer, (int)$segment->getId(), $segment);
    }

    // update cache for subscribers without segment
    $this->recalculateSegmentCache($timer, 0);

    $this->recalculateHomepageCache($timer);

    // remove redundancies from cache
    $this->cronHelper->enforceExecutionLimit($timer);
    $this->subscribersCountsController->removeRedundancyFromStatisticsCache();

    return true;
  }

  private function recalculateSegmentCache($timer, int $segmentId, ?SegmentEntity $segment = null): void {
    $this->cronHelper->enforceExecutionLimit($timer);
    $now = Carbon::now();
    $item = $this->transientCache->getItem(TransientCache::SUBSCRIBERS_STATISTICS_COUNT_KEY, $segmentId);
    if ($item === null || !isset($item['created_at']) || $now->diffInMinutes($item['created_at']) > self::EXPIRATION_IN_MINUTES) {
      if ($segment) {
        $this->subscribersCountsController->recalculateSegmentStatisticsCache($segment);
      } else {
        $this->subscribersCountsController->recalculateSubscribersWithoutSegmentStatisticsCache();
      }
    }
  }

  private function recalculateHomepageCache($timer): void {
    $this->cronHelper->enforceExecutionLimit($timer);
    $now = Carbon::now();
    $item = $this->transientCache->getItem(TransientCache::SUBSCRIBERS_HOMEPAGE_STATISTICS_COUNT_KEY, 0);
    if ($item === null || !isset($item['created_at']) || $now->diffInMinutes($item['created_at']) > self::EXPIRATION_IN_MINUTES) {
      $this->cronHelper->enforceExecutionLimit($timer);
      $this->subscribersCountsController->recalculateHomepageStatisticsCache();
    }
  }

  public function getNextRunDate() {
    return Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
  }

  public function shouldBeScheduled(): bool {
    $scheduledOrRunningTask = $this->scheduledTasksRepository->findScheduledOrRunningTask(self::TASK_TYPE);
    if ($scheduledOrRunningTask) {
      return false;
    }
    $now = Carbon::now();
    $oldestCreatedAt = $this->transientCache->getOldestCreatedAt(TransientCache::SUBSCRIBERS_STATISTICS_COUNT_KEY);
    return $oldestCreatedAt === null || $now->diffInMinutes($oldestCreatedAt) > self::EXPIRATION_IN_MINUTES;
  }
}
