<?php declare(strict_types = 1);

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\ORM\EntityManager;

class DBCollationChecker {

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
  }

  /**
   * If two columns have incompatible collations returns MySQL's COLLATE command to be used with the target table column.
   * e.g. WHERE source_table.column = target_table.column COLLATE xyz
   *
   * In MySQL, if you have the same charset and collation in joined tables' columns it's perfect;
   * if you have different charsets, utf8 and utf8mb4, it works too; but if you have the same charset
   * with different collations, e.g. utf8mb4_unicode_ci and utf8mb4_unicode_520_ci, it will fail
   * with an 'Illegal mix of collations' error.
   */
  public function getCollateIfNeeded(string $sourceTable, string $sourceColumn, string $targetTable, string $targetColumn): string {
    $connection = $this->entityManager->getConnection();
    $sourceColumnData = $connection->executeQuery("SHOW FULL COLUMNS FROM $sourceTable WHERE Field = '$sourceColumn';")->fetchAllAssociative();
    $sourceCollation = $sourceColumnData[0]['Collation'] ?? '';
    $targetColumnData = $connection->executeQuery("SHOW FULL COLUMNS FROM $targetTable WHERE Field = '$targetColumn';")->fetchAllAssociative();
    $targetCollation = $targetColumnData[0]['Collation'] ?? '';
    if ($sourceCollation === $targetCollation) {
      return '';
    }
    list($sourceCharset) = explode('_', $sourceCollation);
    list($targetCharset) = explode('_', $targetCollation);
    if ($sourceCharset === $targetCharset) {
      return "COLLATE $sourceCollation";
    }
    return '';
  }
}
