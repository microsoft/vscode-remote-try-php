<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Migrator\DbMigration;

class Migration_20230215_050813 extends DbMigration {
  public function run(): void {
    $this->subjectsMigration();
    $this->addMetaColumnToAutomations();
  }

  private function addMetaColumnToAutomations(): void {

    global $wpdb;
    $tableName = esc_sql($wpdb->prefix . 'mailpoet_automations');
    if ($this->columnExists($tableName, 'meta')) {
      return;
    }
    $this->connection->executeQuery("ALTER TABLE $tableName ADD COLUMN `meta` LONGTEXT DEFAULT NULL AFTER `status`");
    $this->connection->executeQuery("UPDATE $tableName SET `meta` = '{\"mailpoet:run-once-per-subscriber\":true}'");
  }

  private function subjectsMigration(): void {
    $this->createTable('automation_run_subjects', [
      '`id` int(11) unsigned NOT NULL AUTO_INCREMENT',
      '`automation_run_id` int(11) unsigned NOT NULL',
      '`key` varchar(191)',
      '`args` longtext',
      '`hash` varchar(191)',
      'PRIMARY KEY  (id)',
      'index (automation_run_id)',
      'index (hash)',
    ]);
    $this->moveSubjectData();
    $this->dropSubjectColumn();
  }

  private function moveSubjectData(): void {
    global $wpdb;
    $runTable = $wpdb->prefix . 'mailpoet_automation_runs';
    $subjectTable = $wpdb->prefix . 'mailpoet_automation_run_subjects';
    if (!$this->columnExists($runTable, 'subjects')) {
      return;
    }
    $sql = "SELECT id,subjects FROM $runTable";
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (!is_array($results) || !$results) {
      return;
    }

    foreach ($results as $result) {
      $subjects = $result['subjects'];
      if (!$subjects) {
        continue;
      }
      $subjects = json_decode($subjects, true);
      if (!is_array($subjects) || !$subjects) {
        continue;
      }
      $values = [];
      foreach ($subjects as $subject) {
        $values[] = (string)$wpdb->prepare("(%d,%s,%s)", $result['id'], $subject['key'], json_encode($subject['args']));
      }
      $sql = sprintf("INSERT INTO $subjectTable (`automation_run_id`, `key`, `args`) VALUES %s", implode(',', $values));
      if ($wpdb->query($sql) === false) {
        continue;
      }

      $sql = $wpdb->prepare('UPDATE ' . $runTable . ' SET subjects = NULL WHERE id = %d', $result['id']);
      $wpdb->query($sql);
    }
  }

  private function dropSubjectColumn(): void {
    global $wpdb;
    $tableName = esc_sql($wpdb->prefix . 'mailpoet_automation_runs');
    if (!$this->columnExists($tableName, 'subjects')) {
      return;
    }

    $wpdb->query("ALTER TABLE $tableName DROP COLUMN subjects");
  }
}
