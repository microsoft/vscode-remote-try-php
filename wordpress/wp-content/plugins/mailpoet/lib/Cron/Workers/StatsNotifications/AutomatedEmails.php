<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\StatsNotifications;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer;
use MailPoet\Cron\Workers\SimpleWorker;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Statistics\NewsletterStatistics;
use MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class AutomatedEmails extends SimpleWorker {
  const TASK_TYPE = 'stats_notification_automated_emails';

  /** @var MailerFactory */
  private $mailerFactory;

  /** @var SettingsController */
  private $settings;

  /** @var Renderer */
  private $renderer;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var NewslettersRepository */
  private $repository;

  /** @var NewsletterStatisticsRepository */
  private $newsletterStatisticsRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    MailerFactory $mailerFactory,
    Renderer $renderer,
    SettingsController $settings,
    NewslettersRepository $repository,
    NewsletterStatisticsRepository $newsletterStatisticsRepository,
    MetaInfo $mailerMetaInfo,
    TrackingConfig $trackingConfig
  ) {
    parent::__construct();
    $this->mailerFactory = $mailerFactory;
    $this->settings = $settings;
    $this->renderer = $renderer;
    $this->mailerMetaInfo = $mailerMetaInfo;
    $this->repository = $repository;
    $this->newsletterStatisticsRepository = $newsletterStatisticsRepository;
    $this->trackingConfig = $trackingConfig;
  }

  public function checkProcessingRequirements() {
    $settings = $this->settings->get(Worker::SETTINGS_KEY);
    if (!is_array($settings)) {
      return false;
    }
    if (!isset($settings['automated'])) {
      return false;
    }
    if (!isset($settings['address'])) {
      return false;
    }
    if (empty(trim($settings['address']))) {
      return false;
    }
    if (!$this->trackingConfig->isEmailTrackingEnabled()) {
      return false;
    }
    return (bool)$settings['automated'];
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    try {
      $settings = $this->settings->get(Worker::SETTINGS_KEY);
      $newsletters = $this->getNewsletters();
      if ($newsletters) {
        $extraParams = [
          'meta' => $this->mailerMetaInfo->getStatsNotificationMetaInfo(),
        ];
        $this->mailerFactory->getDefaultMailer()->send($this->constructNewsletter($newsletters), $settings['address'], $extraParams);
      }
    } catch (\Exception $e) {
      if (WP_DEBUG) {
        throw $e;
      }
    }
    return true;
  }

  /**
   * @param array<int, array{newsletter: NewsletterEntity, statistics: NewsletterStatistics}> $newsletters
   */
  private function constructNewsletter(array $newsletters): array {
    $context = $this->prepareContext($newsletters);
    return [
      'subject' => __('Your monthly stats are in!', 'mailpoet'),
      'body' => [
        'html' => $this->renderer->render('emails/statsNotificationAutomatedEmails.html', $context),
        'text' => $this->renderer->render('emails/statsNotificationAutomatedEmails.txt', $context),
      ],
    ];
  }

  /**
   * @return array<int, array{newsletter: NewsletterEntity, statistics: NewsletterStatistics}>
   */
  protected function getNewsletters(): array {
    $result = [];
    $newsletters = $this->repository->findActiveByTypes(
      [NewsletterEntity::TYPE_AUTOMATIC, NewsletterEntity::TYPE_WELCOME]
    );
    foreach ($newsletters as $newsletter) {
      $statistics = $this->newsletterStatisticsRepository->getStatistics($newsletter);
      if ($statistics->getTotalSentCount()) {
        $result[] = [
          'statistics' => $statistics,
          'newsletter' => $newsletter,
        ];
      }
    }
    return $result;
  }

  /**
   * @param array<int, array{newsletter: NewsletterEntity, statistics: NewsletterStatistics}> $newsletters
   * @return array
   */
  private function prepareContext(array $newsletters): array {
    $context = [
      'linkSettings' => WPFunctions::get()->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-settings#basics'),
      'newsletters' => [],
    ];
    foreach ($newsletters as $row) {
      $statistics = $row['statistics'];
      $newsletter = $row['newsletter'];
      $clicked = ($statistics->getClickCount() * 100) / $statistics->getTotalSentCount();
      $opened = ($statistics->getOpenCount() * 100) / $statistics->getTotalSentCount();
      $machineOpened = ($statistics->getMachineOpenCount() * 100) / $statistics->getTotalSentCount();
      $unsubscribed = ($statistics->getUnsubscribeCount() * 100) / $statistics->getTotalSentCount();
      $bounced = ($statistics->getBounceCount() * 100) / $statistics->getTotalSentCount();
      $context['newsletters'][] = [
        'linkStats' => WPFunctions::get()->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-newsletters#/stats/' . $newsletter->getId()),
        'clicked' => $clicked,
        'opened' => $opened,
        'machineOpened' => $machineOpened,
        'unsubscribed' => $unsubscribed,
        'bounced' => $bounced,
        'subject' => $newsletter->getSubject(),
      ];
    }
    return $context;
  }

  public function getNextRunDate() {
    $wp = new WPFunctions;
    $date = Carbon::createFromTimestamp($wp->currentTime('timestamp'));
    return $date->endOfMonth()->next(Carbon::MONDAY)->midDay();
  }
}
