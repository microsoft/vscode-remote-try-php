<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Migrator\DbMigration;

class Migration_20230831_143755_Db extends DbMigration {
  public function run(): void {
    global $wpdb;
    $logsTable = $wpdb->prefix . 'mailpoet_automation_run_logs';

    // add "updated_at" column
    if (!$this->columnExists($logsTable, 'updated_at')) {
      $this->connection->executeStatement("ALTER TABLE $logsTable CHANGE `started_at` `started_at` timestamp NULL"); // prevent ER_TOO_MUCH_AUTO_TIMESTAMP_COLS
      $this->connection->executeStatement("ALTER TABLE $logsTable ADD COLUMN updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `started_at`");
    }

    // add "step_type" column
    if (!$this->columnExists($logsTable, 'step_type')) {
      $this->connection->executeStatement("ALTER TABLE $logsTable ADD COLUMN step_type VARCHAR(255) NOT NULL DEFAULT 'action' AFTER `step_id`");
      $this->connection->executeStatement("ALTER TABLE $logsTable ALTER COLUMN step_type DROP DEFAULT");
    }

    // add "step_key" column
    if (!$this->columnExists($logsTable, 'step_key')) {
      $this->connection->executeStatement("ALTER TABLE $logsTable ADD COLUMN step_key VARCHAR(255) NOT NULL DEFAULT '' AFTER `step_type`");
      $this->connection->executeStatement("ALTER TABLE $logsTable ALTER COLUMN step_key DROP DEFAULT");
    }

    // add "run_number" column
    if (!$this->columnExists($logsTable, 'run_number')) {
      $this->connection->executeStatement("ALTER TABLE $logsTable ADD COLUMN run_number INT NOT NULL DEFAULT 1 AFTER `updated_at`");
      $this->connection->executeStatement("ALTER TABLE $logsTable ALTER COLUMN run_number DROP DEFAULT");
    }

    // go through automation data and backfill step keys and trigger logs
    $this->backfillStepKeysAndTriggers();

    // fix mix of 'complete' and 'completed' statuses
    $this->connection->executeStatement("UPDATE $logsTable SET status = 'complete' WHERE status = 'completed'");

    // fix empty values for errors and data
    $this->connection->executeStatement("ALTER TABLE $logsTable CHANGE `data` `data` longtext NOT NULL AFTER run_number");
    $this->connection->executeStatement("UPDATE $logsTable SET data = '{}' WHERE data = '[]' OR data IS NULL");
    $this->connection->executeStatement("UPDATE $logsTable SET error = NULL WHERE error = '[]' OR error IS NULL");

    // remove "completed_at" column (with "updated_at" it's no longer needed), backfill "updated_at"
    if ($this->columnExists($logsTable, 'completed_at')) {
      $this->connection->executeStatement("UPDATE $logsTable SET updated_at = COALESCE(completed_at, started_at)");
      $this->connection->executeStatement("ALTER TABLE $logsTable DROP COLUMN completed_at");
    }

    // add unique index, remove no longer needed index
    if (!$this->indexExists($logsTable, 'automation_run_id_step_id')) {
      $this->connection->executeStatement(
        "DELETE t1 FROM $logsTable as t1, $logsTable as t2 WHERE t1.id < t2.id AND t1.automation_run_id = t2.automation_run_id AND t1.step_id=t2.step_id"
      );
      $this->connection->executeStatement("ALTER TABLE $logsTable ADD UNIQUE automation_run_id_step_id (automation_run_id, step_id)");
    }
    if ($this->indexExists($logsTable, 'automation_run_id')) {
      $this->connection->executeStatement("ALTER TABLE $logsTable DROP INDEX automation_run_id");
    }
  }

  private function backfillStepKeysAndTriggers(): void {
    global $wpdb;
    $logsTable = $wpdb->prefix . 'mailpoet_automation_run_logs';
    $runsTable = $wpdb->prefix . 'mailpoet_automation_runs';
    $versionsTable = $wpdb->prefix . 'mailpoet_automation_versions';

    $triggerAddedMap = [];
    while (true) {
      $data = $this->connection->executeQuery("
        SELECT rl.id, rl.automation_run_id, rl.step_id, rl.started_at, v.steps
        FROM {$logsTable} rl
        JOIN {$runsTable} r ON r.id = rl.automation_run_id
        JOIN {$versionsTable} v ON v.id = r.version_id
        WHERE rl.step_key = ''
        ORDER BY rl.id ASC
        LIMIT 50
      ")->fetchAllAssociative();

      if (count($data) === 0) {
        break;
      }

      $queries = [];
      /** @var array<int, array{id:int, automation_run_id:int, step_id:int, started_at:string, steps:string}> $data */
      foreach ($data as $item) {
        /** @var array $steps */
        $steps = json_decode(strval($item['steps']), true);
        $id = intval($item['id']);
        $stepId = strval($item['step_id']);
        $stepKey = strval($steps[$stepId]['key'] ?? 'unknown');
        $triggerId = $steps['root']['next_steps'][0]['id'];
        $triggerKey = $steps['root']['next_steps'][0]['key'];

        $queries[] = "UPDATE {$logsTable} SET step_key = '{$stepKey}' WHERE id = {$id}";

        // backfill triggers
        $runId = intval($item['automation_run_id']);
        if (!isset($triggerAddedMap[$runId])) {
          $startedAt = strval($item['started_at']);
          $date = "DATE_SUB('$startedAt', INTERVAL 1 SECOND)";
          $queries[] = "
            INSERT INTO {$logsTable} (automation_run_id, step_id, step_type, step_key, status, started_at, updated_at, run_number, data)
            VALUES ($runId, '$triggerId', 'trigger', '$triggerKey', 'complete', $date, $date, 1, '{}')
          ";
          $triggerAddedMap[$runId] = true;
        }
      }

      $this->connection->executeStatement(implode(';', $queries));
    }
  }
}
