<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\ActionScheduler\ActionScheduler as CronActionScheduler;
use MailPoet\Cron\CronTrigger;
use MailPoet\InvalidStateException;
use MailPoet\Migrator\Migrator;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Notices\DisabledMailFunctionNotice;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\DBAL\Connection;

class Activator {
  public const TRANSIENT_ACTIVATE_KEY = 'mailpoet_activator_activate';
  private const TRANSIENT_EXPIRATION = 120; // seconds

  /** @var Connection */
  private $connection;

  /** @var SettingsController */
  private $settings;

  /** @var Populator */
  private $populator;

  /** @var WPFunctions */
  private $wp;

  /** @var Migrator */
  private $migrator;

  /** @var CronActionScheduler */
  private $cronActionSchedulerRunner;

  public function __construct(
    Connection $connection,
    SettingsController $settings,
    Populator $populator,
    WPFunctions $wp,
    Migrator $migrator,
    CronActionScheduler $cronActionSchedulerRunner
  ) {
    $this->connection = $connection;
    $this->settings = $settings;
    $this->populator = $populator;
    $this->wp = $wp;
    $this->migrator = $migrator;
    $this->cronActionSchedulerRunner = $cronActionSchedulerRunner;
  }

  public function activate() {
    $isRunning = $this->wp->getTransient(self::TRANSIENT_ACTIVATE_KEY);
    if ($isRunning === false) {
      $this->lockActivation();
      try {
        $this->processActivate();
      } finally {
        $this->unlockActivation();
      }
    } else {
      throw new InvalidStateException(__('MailPoet version update is in progress, please refresh the page in a minute.', 'mailpoet'));
    }
  }

  private function lockActivation(): void {
    $this->wp->setTransient(self::TRANSIENT_ACTIVATE_KEY, '1', self::TRANSIENT_EXPIRATION);
  }

  private function unlockActivation(): void {
    $this->wp->deleteTransient(self::TRANSIENT_ACTIVATE_KEY);
  }

  private function processActivate(): void {
    $this->migrator->run();
    $this->deactivateCronActions();
    $this->populator->up();
    $this->updateDbVersion();

    $caps = new Capabilities();
    $caps->setupWPCapabilities();

    $localizer = new Localizer();
    $localizer->forceInstallLanguagePacks($this->wp);

    $this->checkForDisabledMailFunction();
  }

  public function deactivate() {
    $this->lockActivation();
    $this->deleteAllMailPoetTablesAndData();

    $caps = new Capabilities();
    $caps->removeWPCapabilities();
    $this->unlockActivation();
  }

  /**
   * Deactivate action scheduler cron actions when the migration run.
   * This should prevent processing actions during migrations.
   * They are later re-activated in CronTrigger
   *
   * @return void
   */
  private function deactivateCronActions(): void {
    $currentMethod = $this->settings->get(CronTrigger::SETTING_NAME . '.method');
    if ($currentMethod !== CronTrigger::METHOD_ACTION_SCHEDULER) {
      return;
    }
    $this->cronActionSchedulerRunner->unscheduleAllCronActions();
  }

  public function updateDbVersion() {
    try {
      $currentDbVersion = $this->settings->get('db_version');
    } catch (\Exception $e) {
      $currentDbVersion = null;
    }

    $this->settings->set('db_version', Env::$version);

    // if current db version and plugin version differ, log an update
    if (version_compare((string)$currentDbVersion, Env::$version) !== 0) {
      $updatesLog = (array)$this->settings->get('updates_log', []);
      $updatesLog[] = [
        'previous_version' => $currentDbVersion,
        'new_version' => Env::$version,
        'date' => date('Y-m-d H:i:s'),
      ];
      $this->settings->set('updates_log', $updatesLog);
    }
  }

  private function checkForDisabledMailFunction() {
    $version = $this->settings->get('version');

    if (!is_null($version)) return; // not a new user

    // check for valid mail function on new installs
    $this->settings->set(DisabledMailFunctionNotice::QUEUE_DISABLED_MAIL_FUNCTION_CHECK, true);
  }

  private function deleteAllMailPoetTablesAndData(): void {
    $prefix = Env::$dbPrefix;
    if (!$prefix) {
      throw InvalidStateException::create()->withMessage('No database table prefix was set.');
    }

    // list all MailPoet tables by prefix
    $prefixSql = $this->wp->escSql($prefix);
    $tables = $this->connection->executeQuery("SHOW TABLES LIKE '$prefixSql%'")->fetchFirstColumn();

    // drop all MailPoet tables in a single query
    $tablesSql = implode(
      ',',
      array_map(function ($table): string {
        /** @var string $table */
        return $this->wp->escSql(strval($table));
      }, $tables)
    );

    $this->connection->executeStatement("
      SET foreign_key_checks = 0;
      DROP TABLE IF EXISTS $tablesSql;
      SET foreign_key_checks = 1;
    ");
  }
}
