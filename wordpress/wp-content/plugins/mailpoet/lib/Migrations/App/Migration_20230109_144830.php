<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\MailerLog;
use MailPoet\Migrator\AppMigration;

class Migration_20230109_144830 extends AppMigration {
  /**
   * Due to a bug https://mailpoet.atlassian.net/browse/MAILPOET-4940 some users may have
   * paused sending without having the error message and they have no way to resume sending.
   * This migration will unpause sending for all users who have paused sending and have no error message.
   */
  public function run(): void {
    $mailerLog = MailerLog::getMailerLog();
    if (isset($mailerLog['status']) && $mailerLog['status'] === MailerLog::STATUS_PAUSED && !isset($mailerLog['error'])) {
      $mailerLog['status'] = null;
      MailerLog::updateMailerLog($mailerLog);
    }
  }
}
