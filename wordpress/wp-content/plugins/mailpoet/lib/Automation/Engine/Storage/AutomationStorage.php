<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Storage;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Integration\Trigger;
use wpdb;

/**
 * @phpstan-type VersionDate array{id: int, created_at: \DateTimeImmutable}
 */
class AutomationStorage {
  /** @var string */
  private $automationsTable;

  /** @var string */
  private $versionsTable;

  /** @var string */
  private $triggersTable;

  /** @var string */
  private $runsTable;

  /** @var string */
  private $subjectsTable;

  /** @var wpdb */
  private $wpdb;

  public function __construct() {
    global $wpdb;
    $this->automationsTable = $wpdb->prefix . 'mailpoet_automations';
    $this->versionsTable = $wpdb->prefix . 'mailpoet_automation_versions';
    $this->triggersTable = $wpdb->prefix . 'mailpoet_automation_triggers';
    $this->runsTable = $wpdb->prefix . 'mailpoet_automation_runs';
    $this->subjectsTable = $wpdb->prefix . 'mailpoet_automation_run_subjects';
    $this->wpdb = $wpdb;
  }

  public function createAutomation(Automation $automation): int {
    $automationHeaderData = $this->getAutomationHeaderData($automation);
    unset($automationHeaderData['id']);
    $result = $this->wpdb->insert($this->automationsTable, $automationHeaderData);
    if (!$result) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
    $id = $this->wpdb->insert_id;
    $this->insertAutomationVersion($id, $automation);
    $this->insertAutomationTriggers($id, $automation);
    return $id;
  }

  public function updateAutomation(Automation $automation): void {
    $oldRecord = $this->getAutomation($automation->getId());
    if ($oldRecord && $oldRecord->equals($automation)) {
      return;
    }
    $result = $this->wpdb->update($this->automationsTable, $this->getAutomationHeaderData($automation), ['id' => $automation->getId()]);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
    $this->insertAutomationVersion($automation->getId(), $automation);
    $this->insertAutomationTriggers($automation->getId(), $automation);
  }

  /**
   * @param int $automationId
   * @return VersionDate[]
   * @throws \Exception
   */
  public function getAutomationVersionDates(int $automationId): array {
    $versionsTable = esc_sql($this->versionsTable);
    /** @var literal-string $sql */
    $sql = "
      SELECT id, created_at
      FROM $versionsTable
      WHERE automation_id = %d
      ORDER BY id DESC
    ";
    $query = (string)$this->wpdb->prepare($sql, $automationId);
    $data = $this->wpdb->get_results($query, ARRAY_A);
    return is_array($data) ? array_map(
      function($row): array {
        /** @var array{id: string, created_at: string} $row */
        return [
          'id' => absint($row['id']),
          'created_at' => new \DateTimeImmutable($row['created_at']),
        ];
      },
      $data
    ) : [];
  }

  /**
   * @param int[] $versionIds
   * @return Automation[]
   */
  public function getAutomationWithDifferentVersions(array $versionIds): array {
    $versionIds = array_map('intval', $versionIds);
    if (!$versionIds) {
      return [];
    }
    $automationsTable = esc_sql($this->automationsTable);
    $versionsTable = esc_sql($this->versionsTable);
    /** @var literal-string $sql */
    $sql = "
      SELECT a.*, v.id AS version_id, v.steps
      FROM $automationsTable as a, $versionsTable as v
      WHERE v.automation_id = a.id AND v.id IN (" . implode(',', array_fill(0, count($versionIds), '%d')) . ")
      ORDER BY v.id DESC
    ";
    $query = (string)$this->wpdb->prepare($sql, ...$versionIds);
    $data = $this->wpdb->get_results($query, ARRAY_A);
    return is_array($data) ? array_map(
      function($row): Automation {
        return Automation::fromArray((array)$row);
      },
      $data
    ) : [];
  }

  public function getAutomation(int $automationId, int $versionId = null): ?Automation {
    $automationsTable = esc_sql($this->automationsTable);
    $versionsTable = esc_sql($this->versionsTable);

    if ($versionId) {
      $automations = $this->getAutomationWithDifferentVersions([$versionId]);
      return $automations ? $automations[0] : null;
    }
    /** @var literal-string $sql */
    $sql = "
      SELECT a.*, v.id AS version_id, v.steps
      FROM $automationsTable as a, $versionsTable as v
      WHERE v.automation_id = a.id AND a.id = %d
      ORDER BY v.id DESC
      LIMIT 1
    ";
    $query = (string)$this->wpdb->prepare($sql, $automationId);
    $data = $this->wpdb->get_row($query, ARRAY_A);
    return $data ? Automation::fromArray((array)$data) : null;
  }

  /** @return Automation[] */
  public function getAutomations(array $status = null): array {
    $automationsTable = esc_sql($this->automationsTable);
    $versionsTable = esc_sql($this->versionsTable);

    $statusFilter = $status ? 'AND a.status IN(' . implode(',', array_fill(0, count($status), '%s')) . ')' : '';
    /** @var literal-string $sql */
    $sql = "
      SELECT a.*, v.id AS version_id, v.steps
      FROM $automationsTable AS a
      INNER JOIN $versionsTable as v ON (v.automation_id = a.id)
      WHERE v.id = (
        SELECT MAX(id) FROM $versionsTable WHERE automation_id = v.automation_id
      )
      $statusFilter
      ORDER BY a.id DESC
    ";

    $query = $status ? (string)$this->wpdb->prepare($sql, ...$status) : $sql;
    $data = $this->wpdb->get_results($query, ARRAY_A);
    return array_map(function ($automationData) {
      /** @var array $automationData - for PHPStan because it conflicts with expected callable(mixed): mixed)|null */
      return Automation::fromArray($automationData);
    }, (array)$data);
  }

  /** @return int[] */
  public function getAutomationIdsBySubject(Subject $subject, array $runStatus = null, int $inTheLastSeconds = null): array {
    $automationsTable = esc_sql($this->automationsTable);
    $runsTable = esc_sql($this->runsTable);
    $subjectTable = esc_sql($this->subjectsTable);

    $statusFilter = $runStatus ? 'AND r.status IN(' . implode(',', array_fill(0, count($runStatus), '%s')) . ')' : '';
    $inTheLastFilter = isset($inTheLastSeconds) ? 'AND r.created_at > DATE_SUB(NOW(), INTERVAL %d SECOND)' : '';

    /** @var literal-string $sql */
    $sql = "
      SELECT DISTINCT a.id
      FROM $automationsTable a
      INNER JOIN $runsTable r ON r.automation_id = a.id
      INNER JOIN $subjectTable s ON s.automation_run_id = r.id
      WHERE s.hash = %s
      $statusFilter
      $inTheLastFilter
      ORDER BY a.id DESC
    ";
    $query = (string)$this->wpdb->prepare(
      $sql,
      array_merge(
        [$subject->getHash()],
        $runStatus ?? [],
        isset($inTheLastSeconds) ? [intval($inTheLastSeconds)] : [],
      )
    );
    return array_map('intval', $this->wpdb->get_col($query));
  }

  public function getAutomationCount(): int {
    $automationsTable = esc_sql($this->automationsTable);
    return (int)$this->wpdb->get_var("SELECT COUNT(*) FROM $automationsTable");
  }

  /** @return string[] */
  public function getActiveTriggerKeys(): array {
    $automationsTable = esc_sql($this->automationsTable);
    $triggersTable = esc_sql($this->triggersTable);

    /** @var literal-string $sql */
    $sql = "
        SELECT DISTINCT t.trigger_key
        FROM {$automationsTable} AS a
        JOIN $triggersTable as t
        WHERE a.status = %s AND a.id = t.automation_id
        ORDER BY trigger_key DESC
      ";
    $query = (string)$this->wpdb->prepare($sql, Automation::STATUS_ACTIVE);
    return $this->wpdb->get_col($query);
  }

  /** @return Automation[] */
  public function getActiveAutomationsByTrigger(Trigger $trigger): array {
    return $this->getActiveAutomationsByTriggerKey($trigger->getKey());
  }

  public function getActiveAutomationsByTriggerKey(string $triggerKey): array {

    $automationsTable = esc_sql($this->automationsTable);
    $versionsTable = esc_sql($this->versionsTable);
    $triggersTable = esc_sql($this->triggersTable);

    /** @var literal-string $sql */
    $sql = "
        SELECT a.*, v.id AS version_id, v.steps
        FROM $automationsTable AS a
        INNER JOIN $triggersTable as t ON (t.automation_id = a.id)
        INNER JOIN $versionsTable as v ON (v.automation_id = a.id)
        WHERE a.status = %s
        AND t.trigger_key = %s
        AND v.id = (
          SELECT MAX(id) FROM $versionsTable WHERE automation_id = v.automation_id
        )
      ";
    $query = (string)$this->wpdb->prepare($sql, Automation::STATUS_ACTIVE, $triggerKey);

    $data = $this->wpdb->get_results($query, ARRAY_A);
    return array_map(function ($automationData) {
      /** @var array $automationData - for PHPStan because it conflicts with expected callable(mixed): mixed)|null */
      return Automation::fromArray($automationData);
    }, (array)$data);
  }

  public function getCountOfActiveByTriggerKeysAndAction(array $triggerKeys, string $actionKey): int {
    $automationsTable = esc_sql($this->automationsTable);
    $versionsTable = esc_sql($this->versionsTable);
    $triggersTable = esc_sql($this->triggersTable);

    $triggerKeysPlaceholders = implode(',', array_fill(0, count($triggerKeys), '%s'));
    $queryArgs = array_merge(
      $triggerKeys,
      [
        Automation::STATUS_ACTIVE,
        '%"' . $this->wpdb->esc_like($actionKey) . '"%',
      ]
    );
    // Using the phpcs:ignore because the query arguments count is dynamic and passed via an array but the code sniffer sees only one argument
    /** @var literal-string $sql */
    $sql = "
        SELECT count(*)
        FROM $automationsTable AS a
        INNER JOIN $triggersTable as t ON (t.automation_id = a.id) AND t.trigger_key IN ({$triggerKeysPlaceholders})
        INNER JOIN $versionsTable as v ON v.id = (SELECT MAX(id) FROM $versionsTable WHERE automation_id = a.id)
        WHERE a.status = %s
        AND v.steps LIKE %s
      ";
    $query = (string)$this->wpdb->prepare($sql, $queryArgs);

    return (int)$this->wpdb->get_var($query);
  }

  public function deleteAutomation(Automation $automation): void {
    $automationRunsTable = esc_sql($this->runsTable);
    $automationRunLogsTable = esc_sql($this->wpdb->prefix . 'mailpoet_automation_run_logs');
    $automationId = $automation->getId();
    /** @var literal-string $sql */
    $sql = "
        DELETE FROM $automationRunLogsTable
        WHERE automation_run_id IN (
          SELECT id
          FROM $automationRunsTable
          WHERE automation_id = %d
        )
      ";
    $runLogsQuery = (string)$this->wpdb->prepare($sql, $automationId);

    $logsDeleted = $this->wpdb->query($runLogsQuery);
    if ($logsDeleted === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    $runsDeleted = $this->wpdb->delete($this->runsTable, ['automation_id' => $automationId]);
    if ($runsDeleted === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    $versionsDeleted = $this->wpdb->delete($this->versionsTable, ['automation_id' => $automationId]);
    if ($versionsDeleted === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    $triggersDeleted = $this->wpdb->delete($this->triggersTable, ['automation_id' => $automationId]);
    if ($triggersDeleted === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    $automationDeleted = $this->wpdb->delete($this->automationsTable, ['id' => $automationId]);
    if ($automationDeleted === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }

  public function truncate(): void {
    $automationsTable = esc_sql($this->automationsTable);
    $result = $this->wpdb->query("TRUNCATE {$automationsTable}");
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    $versionsTable = esc_sql($this->versionsTable);
    $result = $this->wpdb->query("TRUNCATE {$versionsTable}");
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }

    $triggersTable = esc_sql($this->triggersTable);
    $result = $this->wpdb->query("TRUNCATE {$triggersTable}");
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }

  public function getNameColumnLength(): int {
    $nameColumnLengthInfo = $this->wpdb->get_col_length($this->automationsTable, 'name');
    return is_array($nameColumnLengthInfo)
      ? $nameColumnLengthInfo['length'] ?? 255
      : 255;
  }

  private function getAutomationHeaderData(Automation $automation): array {
    $automationHeader = $automation->toArray();
    unset($automationHeader['steps']);
    return $automationHeader;
  }

  private function insertAutomationVersion(int $automationId, Automation $automation): void {
    $dateString = (new DateTimeImmutable())->format(DateTimeImmutable::W3C);
    $data = [
      'automation_id' => $automationId,
      'steps' => $automation->toArray()['steps'],
      'created_at' => $dateString,
      'updated_at' => $dateString,
    ];
    $result = $this->wpdb->insert($this->versionsTable, $data);
    if (!$result) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }

  private function insertAutomationTriggers(int $automationId, Automation $automation): void {
    $triggerKeys = [];
    foreach ($automation->getSteps() as $step) {
      if ($step->getType() === Step::TYPE_TRIGGER) {
        $triggerKeys[] = $step->getKey();
      }
    }

    $triggersTable = esc_sql($this->triggersTable);

    // insert/update
    if ($triggerKeys) {
      $placeholders = implode(',', array_fill(0, count($triggerKeys), '(%d, %s)'));
      /** @var literal-string $sql */
      $sql = "INSERT IGNORE INTO {$triggersTable} (automation_id, trigger_key) VALUES {$placeholders}";
      $query = (string)$this->wpdb->prepare(
        $sql,
        array_merge(
          ...array_map(function (string $key) use ($automationId) {
            return [$automationId, $key];
          }, $triggerKeys)
        )
      );

      $result = $this->wpdb->query($query);
      if ($result === false) {
        throw Exceptions::databaseError($this->wpdb->last_error);
      }
    }

    // delete
    $placeholders = implode(',', array_fill(0, count($triggerKeys), '%s'));
    if ($triggerKeys) {
      /** @var literal-string $sql */
      $sql = "DELETE FROM {$triggersTable} WHERE automation_id = %d AND trigger_key NOT IN ({$placeholders})";
      $query = (string)$this->wpdb->prepare($sql, array_merge([$automationId], $triggerKeys));
    } else {
      /** @var literal-string $sql */
      $sql = "DELETE FROM {$triggersTable} WHERE automation_id = %d";
      $query = (string)$this->wpdb->prepare($sql, $automationId);
    }

    $result = $this->wpdb->query($query);
    if ($result === false) {
      throw Exceptions::databaseError($this->wpdb->last_error);
    }
  }
}
