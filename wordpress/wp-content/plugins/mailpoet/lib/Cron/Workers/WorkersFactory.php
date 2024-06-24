<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\Automations\AbandonedCartWorker;
use MailPoet\Cron\Workers\Bounce as BounceWorker;
use MailPoet\Cron\Workers\KeyCheck\PremiumKeyCheck as PremiumKeyCheckWorker;
use MailPoet\Cron\Workers\KeyCheck\SendingServiceKeyCheck as SendingServiceKeyCheckWorker;
use MailPoet\Cron\Workers\Scheduler as SchedulerWorker;
use MailPoet\Cron\Workers\SendingQueue\SendingQueue as SendingQueueWorker;
use MailPoet\Cron\Workers\StatsNotifications\AutomatedEmails as StatsNotificationsWorkerForAutomatedEmails;
use MailPoet\Cron\Workers\StatsNotifications\Worker as StatsNotificationsWorker;
use MailPoet\Cron\Workers\WooCommerceSync as WooCommerceSyncWorker;
use MailPoet\DI\ContainerWrapper;

class WorkersFactory {
  public const SIMPLE_WORKER_TYPES = [
    SubscribersCountCacheRecalculation::TASK_TYPE,
    NewsletterTemplateThumbnails::TASK_TYPE,
    ReEngagementEmailsScheduler::TASK_TYPE,
    SubscribersLastEngagement::TASK_TYPE,
    SubscribersEngagementScore::TASK_TYPE,
    WooCommercePastOrders::TASK_TYPE,
    AuthorizedSendingEmailsCheck::TASK_TYPE,
    WooCommerceSyncWorker::TASK_TYPE,
    SubscriberLinkTokens::TASK_TYPE,
    UnsubscribeTokens::TASK_TYPE,
    InactiveSubscribers::TASK_TYPE,
    SubscribersEmailCount::TASK_TYPE,
    StatsNotificationsWorkerForAutomatedEmails::TASK_TYPE,
    StatsNotificationsWorker::TASK_TYPE,
    BackfillEngagementData::TASK_TYPE,
    Mixpanel::TASK_TYPE,
  ];

  /** @var ContainerWrapper */
  private $container;

  public function __construct(
    ContainerWrapper $container
  ) {
    $this->container = $container;
  }

  /** @return SchedulerWorker */
  public function createScheduleWorker() {
    return $this->container->get(SchedulerWorker::class);
  }

  /** @return SendingQueueWorker */
  public function createQueueWorker() {
    return $this->container->get(SendingQueueWorker::class);
  }

  /** @return StatsNotificationsWorker */
  public function createStatsNotificationsWorker() {
    return $this->container->get(StatsNotificationsWorker::class);
  }

  /** @return StatsNotificationsWorkerForAutomatedEmails */
  public function createStatsNotificationsWorkerForAutomatedEmails() {
    return $this->container->get(StatsNotificationsWorkerForAutomatedEmails::class);
  }

  /** @return SendingServiceKeyCheckWorker */
  public function createSendingServiceKeyCheckWorker() {
    return $this->container->get(SendingServiceKeyCheckWorker::class);
  }

  /** @return PremiumKeyCheckWorker */
  public function createPremiumKeyCheckWorker() {
    return $this->container->get(PremiumKeyCheckWorker::class);
  }

  /** @return BounceWorker */
  public function createBounceWorker() {
    return $this->container->get(BounceWorker::class);
  }

  /** @return WooCommerceSyncWorker */
  public function createWooCommerceSyncWorker() {
    return $this->container->get(WooCommerceSyncWorker::class);
  }

  /** @return ExportFilesCleanup */
  public function createExportFilesCleanupWorker() {
    return $this->container->get(ExportFilesCleanup::class);
  }

  /** @return Beamer */
  public function createBeamerkWorker() {
    return $this->container->get(Beamer::class);
  }

  /** @return InactiveSubscribers */
  public function createInactiveSubscribersWorker() {
    return $this->container->get(InactiveSubscribers::class);
  }

  /** @return UnsubscribeTokens */
  public function createUnsubscribeTokensWorker() {
    return $this->container->get(UnsubscribeTokens::class);
  }

  /** @return SubscriberLinkTokens */
  public function createSubscriberLinkTokensWorker() {
    return $this->container->get(SubscriberLinkTokens::class);
  }

  /** @return SubscribersEngagementScore */
  public function createSubscribersEngagementScoreWorker() {
    return $this->container->get(SubscribersEngagementScore::class);
  }

  /** @return SubscribersLastEngagement */
  public function createSubscribersLastEngagementWorker() {
    return $this->container->get(SubscribersLastEngagement::class);
  }

  /** @return AuthorizedSendingEmailsCheck */
  public function createAuthorizedSendingEmailsCheckWorker() {
    return $this->container->get(AuthorizedSendingEmailsCheck::class);
  }

  /** @return WooCommercePastOrders */
  public function createWooCommercePastOrdersWorker() {
    return $this->container->get(WooCommercePastOrders::class);
  }

  /** @return SubscribersCountCacheRecalculation */
  public function createSubscribersCountCacheRecalculationWorker() {
    return $this->container->get(SubscribersCountCacheRecalculation::class);
  }

  /** @return ReEngagementEmailsScheduler */
  public function createReEngagementEmailsSchedulerWorker() {
    return $this->container->get(ReEngagementEmailsScheduler::class);
  }

  /** @return SubscribersStatsReport */
  public function createSubscribersStatsReportWorker() {
    return $this->container->get(SubscribersStatsReport::class);
  }

  /** @return NewsletterTemplateThumbnails */
  public function createNewsletterTemplateThumbnailsWorker() {
    return $this->container->get(NewsletterTemplateThumbnails::class);
  }

  /** @return SubscribersEmailCount */
  public function createSubscribersEmailCountsWorker() {
    return $this->container->get(SubscribersEmailCount::class);
  }

  /** @return AbandonedCartWorker */
  public function createAbandonedCartWorker() {
    return $this->container->get(AbandonedCartWorker::class);
  }

  /** @return BackfillEngagementData */
  public function createBackfillEngagementDataWorker() {
    return $this->container->get(BackfillEngagementData::class);
  }

  public function createMixpanelWorker() {
    return $this->container->get(Mixpanel::class);
  }
}
