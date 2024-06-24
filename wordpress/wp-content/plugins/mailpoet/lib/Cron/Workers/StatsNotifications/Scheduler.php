<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\StatsNotifications;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\StatsNotificationEntity;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class Scheduler {

  /**
   * How many hours after the newsletter will be the stats notification sent
   * @var int
   */
  const HOURS_TO_SEND_AFTER_NEWSLETTER = 24;

  /** @var SettingsController */
  private $settings;

  private $supportedTypes = [
    NewsletterEntity::TYPE_NOTIFICATION_HISTORY,
    NewsletterEntity::TYPE_STANDARD,
  ];

  /** @var EntityManager */
  private $entityManager;

  /** @var StatsNotificationsRepository */
  private $repository;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    SettingsController $settings,
    EntityManager $entityManager,
    StatsNotificationsRepository $repository,
    TrackingConfig $trackingConfig
  ) {
    $this->settings = $settings;
    $this->entityManager = $entityManager;
    $this->repository = $repository;
    $this->trackingConfig = $trackingConfig;
  }

  public function schedule(NewsletterEntity $newsletter) {
    if (!$this->shouldSchedule($newsletter)) {
      return false;
    }

    $task = new ScheduledTaskEntity();
    $task->setType(Worker::TASK_TYPE);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $task->setScheduledAt($this->getNextRunDate());
    $this->entityManager->persist($task);
    $this->entityManager->flush();

    $statsNotifications = new StatsNotificationEntity($newsletter, $task);
    $this->entityManager->persist($statsNotifications);
    $this->entityManager->flush();
  }

  private function shouldSchedule(NewsletterEntity $newsletter) {
    if ($this->isDisabled()) {
      return false;
    }
    if (!in_array($newsletter->getType(), $this->supportedTypes)) {
      return false;
    }
    if ($this->hasTaskBeenScheduled($newsletter->getId())) {
      return false;
    }
    return true;
  }

  private function isDisabled() {
    $settings = $this->settings->get(Worker::SETTINGS_KEY);
    if (!is_array($settings)) {
      return true;
    }
    if (!isset($settings['enabled'])) {
      return true;
    }
    if (!isset($settings['address'])) {
      return true;
    }
    if (empty(trim($settings['address']))) {
      return true;
    }
    if (!$this->trackingConfig->isEmailTrackingEnabled()) {
      return true;
    }
    return !(bool)$settings['enabled'];
  }

  private function hasTaskBeenScheduled($newsletterId) {
    $existing = $this->repository->findOneByNewsletterId($newsletterId);
    return $existing instanceof StatsNotificationEntity;
  }

  private function getNextRunDate() {
    $date = new Carbon();
    $date->addHours(self::HOURS_TO_SEND_AFTER_NEWSLETTER);
    return $date;
  }
}
