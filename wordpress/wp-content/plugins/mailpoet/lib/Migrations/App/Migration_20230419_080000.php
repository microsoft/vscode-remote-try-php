<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Migrator\AppMigration;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Scheduler\PostNotificationScheduler;

class Migration_20230419_080000 extends AppMigration {
  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var PostNotificationScheduler */
  private $postNotificationScheduler;

  public function run(): void {
    $this->newslettersRepository = $this->container->get(NewslettersRepository::class);
    $this->postNotificationScheduler = $this->container->get(PostNotificationScheduler::class);
    $this->fixPostNotificationScheduleTime();
  }

  /**
   * Because we released PostNotificationScheduler that didn't schedule notifications with the minute resolution,
   * which was added in version 4.10.0, we need to fix the scheduled time for all notifications.
   *
   * Ticket with bug: https://mailpoet.atlassian.net/browse/MAILPOET-5244
   * Ticket with adding minute resolution: https://mailpoet.atlassian.net/browse/MAILPOET-4602
   *
   * @return void
   */
  private function fixPostNotificationScheduleTime() {
    $newsletters = $this->newslettersRepository->findBy(['type' => NewsletterEntity::TYPE_NOTIFICATION]);
    foreach ($newsletters as $newsletter) {
      $this->postNotificationScheduler->processPostNotificationSchedule($newsletter);
    }
  }
}
