<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Storage;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\AutomationRunLog;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\InvalidStateException;
use wpdb;

class AutomationRunLogStorage {
  /** @var string */
  private $table;

  /** @var wpdb */
  private $wpdb;

  public function __construct() {
    global $wpdb;
    $this->table = $wpdb->prefix . 'mailpoet_automation_run_logs';
    $this->wpdb = $wpdb;
  }

  public function createAutomationRunLog(AutomationRunLog $automationRunLog): int {
    $result = $this->wpdb->insert($this->table, $automationRunLog->toArray());
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
    return $this->wpdb->insert_id;
  }

  public function updateAutomationRunLog(AutomationRunLog $automationRunLog): void {
    $result = $this->wpdb->update($this->table, $automationRunLog->toArray(), ['id' => $automationRunLog->getId()]);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }

  public function getAutomationRunStatisticsForAutomationInTimeFrame(int $automationId, string $status, \DateTimeImmutable $after, \DateTimeImmutable $before, int $versionId = null): array {
    $logTable = esc_sql($this->table);
    $runTable = esc_sql($this->wpdb->prefix . 'mailpoet_automation_runs');

    $whereCondition = 'run.automation_id = %d
      AND log.status = %s
      AND run.created_at BETWEEN %s AND %s';
    if ($versionId !== null) {
      $whereCondition .= ' AND run.version_id = %d';
    }
    /** @var literal-string $sql */
    $sql = "SELECT count(log.id) as `count`, log.step_id FROM $logTable AS log
      JOIN $runTable AS run ON log.automation_run_id = run.id
      WHERE $whereCondition
      GROUP BY log.step_id";

    $sql = $versionId ? $this->wpdb->prepare($sql, $automationId, $status, $after->format('Y-m-d H:i:s'), $before->format('Y-m-d H:i:s'), $versionId)
      : $this->wpdb->prepare($sql, $automationId, $status, $after->format('Y-m-d H:i:s'), $before->format('Y-m-d H:i:s'));

    $sql = is_string($sql) ? $sql : "";
    $results = $this->wpdb->get_results($sql, ARRAY_A);
    return is_array($results) ? $results : [];
  }

  public function getAutomationRunLog(int $id): ?AutomationRunLog {
    $table = esc_sql($this->table);
    /** @var literal-string $sql */
    $sql = "SELECT * FROM $table WHERE id = %d";
    $query = $this->wpdb->prepare($sql, $id);

    if (!is_string($query)) {
      throw InvalidStateException::create();
    }

    $result = $this->wpdb->get_row($query, ARRAY_A);

    if ($result) {
      $data = (array)$result;
      return AutomationRunLog::fromArray($data);
    }
    return null;
  }

  public function getAutomationRunLogByRunAndStepId(int $runId, string $stepId): ?AutomationRunLog {
    $table = esc_sql($this->table);
    /** @var literal-string $sql */
    $sql = "SELECT * FROM $table WHERE automation_run_id = %d AND step_id = %s";
    $query = $this->wpdb->prepare($sql, $runId, $stepId);
    if (!is_string($query)) {
      throw InvalidStateException::create();
    }
    $result = $this->wpdb->get_row($query, ARRAY_A);
    return $result ? AutomationRunLog::fromArray((array)$result) : null;
  }

  /**
   * @param int $automationRunId
   * @return AutomationRunLog[]
   * @throws InvalidStateException
   */
  public function getLogsForAutomationRun(int $automationRunId): array {
    $table = esc_sql($this->table);
    /** @var literal-string $sql */
    $sql = "
      SELECT *
      FROM $table
      WHERE automation_run_id = %d
      ORDER BY id ASC
    ";
    $query = $this->wpdb->prepare($sql, $automationRunId);

    if (!is_string($query)) {
      throw InvalidStateException::create();
    }

    $results = $this->wpdb->get_results($query, ARRAY_A);

    if (!is_array($results)) {
      throw InvalidStateException::create();
    }

    if ($results) {
      return array_map(function($data) {
        /** @var array $data - for PHPStan because it conflicts with expected callable(mixed): mixed)|null */
        return AutomationRunLog::fromArray($data);
      }, $results);
    }

    return [];
  }

  public function truncate(): void {
    $table = esc_sql($this->table);
    $sql = "TRUNCATE $table";
    $this->wpdb->query($sql);
  }
}
