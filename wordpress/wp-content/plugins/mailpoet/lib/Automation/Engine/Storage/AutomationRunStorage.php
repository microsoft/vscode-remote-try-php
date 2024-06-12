<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Storage;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationRun;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Exceptions;
use wpdb;

class AutomationRunStorage {
  /** @var string */
  private $table;

  /** @var string */
  private $subjectTable;

  /** @var wpdb */
  private $wpdb;

  public function __construct() {
    global $wpdb;
    $this->table = $wpdb->prefix . 'mailpoet_automation_runs';
    $this->subjectTable = $wpdb->prefix . 'mailpoet_automation_run_subjects';
    $this->wpdb = $wpdb;
  }

  public function createAutomationRun(AutomationRun $automationRun): int {
    $automationTableData = $automationRun->toArray();
    $subjectTableData = $automationTableData['subjects'];
    unset($automationTableData['subjects']);
    $result = $this->wpdb->insert($this->table, $automationTableData);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
    $automationRunId = $this->wpdb->insert_id;

    if (!$subjectTableData) {
      //We allow for AutomationRuns with no subjects.
      return $automationRunId;
    }

    $sql = 'insert into ' . esc_sql($this->subjectTable) . ' (`automation_run_id`, `key`, `args`, `hash`) values %s';
    $values = [];
    foreach ($subjectTableData as $entry) {
      $values[] = (string)$this->wpdb->prepare("(%d,%s,%s,%s)", $automationRunId, $entry['key'], $entry['args'], $entry['hash']);
    }
    $sql = sprintf($sql, implode(',', $values));
    $result = $this->wpdb->query($sql);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    return $automationRunId;
  }

  public function getAutomationRun(int $id): ?AutomationRun {
    $table = esc_sql($this->table);
    $subjectTable = esc_sql($this->subjectTable);
    /** @var literal-string $sql */
    $sql = "SELECT * FROM $table  WHERE id = %d";
    $query = (string)$this->wpdb->prepare($sql, $id);
    $data = $this->wpdb->get_row($query, ARRAY_A);
    if (!is_array($data) || !$data) {
      return null;
    }
    /** @var literal-string $sql */
    $sql = "SELECT * FROM $subjectTable WHERE automation_run_id = %d";
    $query = (string)$this->wpdb->prepare($sql, $id);
    $subjects = $this->wpdb->get_results($query, ARRAY_A);
    $data['subjects'] = is_array($subjects) ? $subjects : [];
    return AutomationRun::fromArray((array)$data);
  }

  /**
   * @param Automation $automation
   * @return AutomationRun[]
   */
  public function getAutomationRunsForAutomation(Automation $automation): array {
    $table = esc_sql($this->table);
    $subjectTable = esc_sql($this->subjectTable);
    /** @var literal-string $sql */
    $sql = "SELECT * FROM $table WHERE automation_id = %d order by id";
    $query = (string)$this->wpdb->prepare($sql, $automation->getId());
    $automationRuns = $this->wpdb->get_results($query, ARRAY_A);
    if (!is_array($automationRuns) || !$automationRuns) {
      return [];
    }

    $automationRunIds = array_column($automationRuns, 'id');

    /** @var literal-string $sql */
    $sql = sprintf(
      "SELECT * FROM $subjectTable WHERE automation_run_id in (%s) order by automation_run_id, id",
      implode(
        ',',
        array_map(
          function() {
            return '%d';
          },
          $automationRunIds
        )
      )
    );

    $query = (string)$this->wpdb->prepare($sql, ...$automationRunIds);
    $subjects = $this->wpdb->get_results($query, ARRAY_A);

    return array_map(
      function($runData) use ($subjects): AutomationRun {
        /** @var array $runData - PHPStan expects as array_map first parameter (callable(mixed): mixed)|null */
        $runData['subjects'] = array_values(array_filter(
          is_array($subjects) ? $subjects : [],
          function($subjectData) use ($runData): bool {
            /** @var array $subjectData - PHPStan expects as array_map first parameter (callable(mixed): mixed)|null */
            return (int)$subjectData['automation_run_id'] === (int)$runData['id'];
          }
        ));
        return AutomationRun::fromArray($runData);
      },
      $automationRuns
    );
  }

  /**
   * @param Automation $automation
   * @return int
   */
  public function getCountByAutomationAndSubject(Automation $automation, Subject $subject): int {
    $table = esc_sql($this->table);
    $subjectTable = esc_sql($this->subjectTable);
    /** @var literal-string $sql */
    $sql = "SELECT count(DISTINCT runs.id) as count from $table as runs
      JOIN $subjectTable as subjects on runs.id = subjects.automation_run_id
      WHERE runs.automation_id = %d
      AND subjects.hash = %s";

    $result = $this->wpdb->get_col(
      (string)$this->wpdb->prepare($sql, $automation->getId(), $subject->getHash())
    );

    return $result ? (int)current($result) : 0;
  }

  public function getCountForAutomation(Automation $automation, string ...$status): int {
    $table = esc_sql($this->table);

    if (!count($status)) {
      /** @var literal-string $sql */
      $sql = "
        SELECT COUNT(id) as count
        FROM $table
        WHERE automation_id = %d
      ";
      $query = (string)$this->wpdb->prepare($sql, $automation->getId());
      $result = $this->wpdb->get_col($query);
      return $result ? (int)current($result) : 0;
    }

    $statusSql = (string)$this->wpdb->prepare(implode(',', array_fill(0, count($status), '%s')), ...$status);
    /** @var literal-string $sql */
    $sql = "
      SELECT COUNT(id) as count
      FROM $table
      WHERE automation_id = %d
      AND status IN ($statusSql)
    ";
    $query = (string)$this->wpdb->prepare($sql, $automation->getId());
    $result = $this->wpdb->get_col($query);
    return $result ? (int)current($result) : 0;
  }

  public function updateStatus(int $id, string $status): void {
    $table = esc_sql($this->table);
    /** @var literal-string $sql */
    $sql = "
      UPDATE $table
      SET status = %s, updated_at = current_timestamp()
      WHERE id = %d
    ";
    $query = (string)$this->wpdb->prepare($sql, $status, $id);
    $result = $this->wpdb->query($query);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }

  public function updateNextStep(int $id, ?string $nextStepId): void {
    $table = esc_sql($this->table);
    /** @var literal-string $sql */
    $sql = "
      UPDATE $table
      SET next_step_id = %s, updated_at = current_timestamp()
      WHERE id = %d
    ";
    $query = (string)$this->wpdb->prepare($sql, $nextStepId, $id);
    $result = $this->wpdb->query($query);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }

  public function getAutomationStepStatisticForTimeFrame(int $automationId, string $status, \DateTimeImmutable $after, \DateTimeImmutable $before, int $versionId = null): array {
    $table = esc_sql($this->table);

    $where = "automation_id = %d
    AND `status` = %s
    AND created_at BETWEEN %s AND %s";
    if ($versionId) {
      $where .= " AND version_id = %d";
    }
    /** @var literal-string $sql */
    $sql = "
      SELECT
        COUNT(id) AS `count`,
        next_step_id
      FROM $table as log
      WHERE $where
      GROUP BY next_step_id
    ";

    $sql = $versionId ?
      $this->wpdb->prepare($sql, $automationId, $status, $after->format('Y-m-d H:i:s'), $before->format('Y-m-d H:i:s'), $versionId) :
      $this->wpdb->prepare($sql, $automationId, $status, $after->format('Y-m-d H:i:s'), $before->format('Y-m-d H:i:s'));
    $sql = is_string($sql) ? $sql : '';
    $result = $this->wpdb->get_results($sql, ARRAY_A);
    return is_array($result) ? $result : [];
  }

  public function truncate(): void {
    $table = esc_sql($this->table);
    $this->wpdb->query("TRUNCATE $table");
    $table = esc_sql($this->subjectTable);
    $this->wpdb->query("TRUNCATE $table");
  }
}
