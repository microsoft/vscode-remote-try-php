<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\StatsNotifications;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer;
use MailPoet\Config\ServicesChecker;
use MailPoet\Cron\CronHelper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterLinkEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatsNotificationEntity;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class Worker {

  const TASK_TYPE = 'stats_notification';
  const SETTINGS_KEY = 'stats_notifications';
  const BATCH_SIZE = 5;

  /** @var Renderer */
  private $renderer;

  /** @var MailerFactory */
  private $mailerFactory;

  /** @var SettingsController */
  private $settings;

  /** @var CronHelper */
  private $cronHelper;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var StatsNotificationsRepository */
  private $repository;

  /** @var EntityManager */
  private $entityManager;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  /** @var NewsletterStatisticsRepository */
  private $newsletterStatisticsRepository;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var ServicesChecker */
  private $servicesChecker;

  public function __construct(
    MailerFactory $mailerFactory,
    Renderer $renderer,
    SettingsController $settings,
    CronHelper $cronHelper,
    MetaInfo $mailerMetaInfo,
    StatsNotificationsRepository $repository,
    NewsletterLinkRepository $newsletterLinkRepository,
    NewsletterStatisticsRepository $newsletterStatisticsRepository,
    EntityManager $entityManager,
    SubscribersFeature $subscribersFeature,
    SubscribersRepository $subscribersRepository,
    ServicesChecker $servicesChecker
  ) {
    $this->renderer = $renderer;
    $this->mailerFactory = $mailerFactory;
    $this->settings = $settings;
    $this->cronHelper = $cronHelper;
    $this->mailerMetaInfo = $mailerMetaInfo;
    $this->repository = $repository;
    $this->entityManager = $entityManager;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
    $this->newsletterStatisticsRepository = $newsletterStatisticsRepository;
    $this->subscribersFeature = $subscribersFeature;
    $this->subscribersRepository = $subscribersRepository;
    $this->servicesChecker = $servicesChecker;
  }

  /** @throws \Exception */
  public function process($timer = false) {
    $timer = $timer ?: microtime(true);
    $settings = $this->settings->get(self::SETTINGS_KEY);
    // Cleanup potential orphaned task created due bug MAILPOET-3015
    $this->repository->deleteOrphanedScheduledTasks();
    foreach ($this->repository->findScheduled(self::BATCH_SIZE) as $statsNotificationEntity) {
      try {
        $extraParams = [
          'meta' => $this->mailerMetaInfo->getStatsNotificationMetaInfo(),
        ];
        $this->mailerFactory->getDefaultMailer()->send($this->constructNewsletter($statsNotificationEntity), $settings['address'], $extraParams);
      } catch (\Exception $e) {
        if (WP_DEBUG) {
          throw $e;
        }
      } finally {
        $task = $statsNotificationEntity->getTask();
        if ($task instanceof ScheduledTaskEntity) {
          $this->markTaskAsFinished($task);
        }
      }
      $this->cronHelper->enforceExecutionLimit($timer);
    }
  }

  private function constructNewsletter(StatsNotificationEntity $statsNotificationEntity) {
    $newsletter = $statsNotificationEntity->getNewsletter();
    if (!$newsletter instanceof NewsletterEntity) {
      throw new \RuntimeException('Missing newsletter entity for statistic notification.');
    }
    $link = $this->newsletterLinkRepository->findTopLinkForNewsletter((int)$newsletter->getId());
    $sendingQueue = $newsletter->getLatestQueue();
    if (!$sendingQueue instanceof SendingQueueEntity) {
      throw new \RuntimeException('Missing sending queue entity for statistic notification.');
    }
    $context = $this->prepareContext($newsletter, $sendingQueue, $link);
    $subject = $sendingQueue->getNewsletterRenderedSubject();
    return [
      // translators: %s is the subject of the email.
      'subject' => sprintf(_x('Stats for email %s', 'title of an automatic email containing statistics (newsletter open rate, click rate, etc)', 'mailpoet'), $subject),
      'body' => [
        'html' => $this->renderer->render('emails/statsNotification.html', $context),
        'text' => $this->renderer->render('emails/statsNotification.txt', $context),
      ],
    ];
  }

  private function prepareContext(NewsletterEntity $newsletter, SendingQueueEntity $sendingQueue, NewsletterLinkEntity $link = null) {
    $statistics = $this->newsletterStatisticsRepository->getStatistics($newsletter);
    $clicked = ($statistics->getClickCount() * 100) / $statistics->getTotalSentCount();
    $opened = ($statistics->getOpenCount() * 100) / $statistics->getTotalSentCount();
    $machineOpened = ($statistics->getMachineOpenCount() * 100) / $statistics->getTotalSentCount();
    $unsubscribed = ($statistics->getUnsubscribeCount() * 100) / $statistics->getTotalSentCount();
    $bounced = ($statistics->getBounceCount() * 100) / $statistics->getTotalSentCount();
    $subject = $sendingQueue->getNewsletterRenderedSubject();
    $subscribersCount = $this->subscribersRepository->getTotalSubscribers();
    $hasValidApiKey = $this->subscribersFeature->hasValidApiKey();
    $context = [
      'subject' => $subject,
      'preheader' => sprintf(
        // translators: %1$s is the percentage of clicks, %2$s the percentage of opens and %3$s the number of unsubscribes.
        _x(
          '%1$s%% clicks, %2$s%% opens, %3$s%% unsubscribes in a nutshell.',
          'newsletter open rate, click rate and unsubscribe rate',
          'mailpoet'
        ),
        number_format($clicked, 2),
        number_format($opened, 2),
        number_format($unsubscribed, 2)
      ),
      'topLinkClicks' => 0,
      'linkSettings' => WPFunctions::get()->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-settings#basics'),
      'linkStats' => WPFunctions::get()->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-newsletters&stats=' . $newsletter->getId()),
      'clicked' => $clicked,
      'opened' => $opened,
      'machineOpened' => $machineOpened,
      'unsubscribed' => $unsubscribed,
      'bounced' => $bounced,
      'subscribersLimitReached' => $this->subscribersFeature->check(),
      'hasValidApiKey' => $hasValidApiKey,
      'subscribersLimit' => $this->subscribersFeature->getSubscribersLimit(),
      'upgradeNowLink' => $hasValidApiKey
        ? 'https://account.mailpoet.com/orders/upgrade/' . $this->servicesChecker->generatePartialApiKey()
        : 'https://account.mailpoet.com/?s=' . ($subscribersCount + 1),
    ];
    if ($link) {
      $context['topLinkClicks'] = $link->getTotalClicksCount();
      $mappings = self::getShortcodeLinksMapping();
      $context['topLink'] = isset($mappings[$link->getUrl()]) ? $mappings[$link->getUrl()] : $link->getUrl();
    }
    return $context;
  }

  private function markTaskAsFinished(ScheduledTaskEntity $task) {
    $task->setStatus(ScheduledTaskEntity::STATUS_COMPLETED);
    $task->setProcessedAt(Carbon::createFromTimestamp(WPFunctions::get()->currentTime('timestamp')));
    $task->setScheduledAt(null);
    $this->entityManager->flush();
  }

  public static function getShortcodeLinksMapping() {
    return [
      NewsletterLinkEntity::UNSUBSCRIBE_LINK_SHORT_CODE => __('Unsubscribe link', 'mailpoet'),
      NewsletterLinkEntity::INSTANT_UNSUBSCRIBE_LINK_SHORT_CODE => __('Unsubscribe link (without confirmation)', 'mailpoet'),
      '[link:subscription_manage_url]' => __('Manage subscription link', 'mailpoet'),
      '[link:newsletter_view_in_browser_url]' => __('View in browser link', 'mailpoet'),
    ];
  }
}
