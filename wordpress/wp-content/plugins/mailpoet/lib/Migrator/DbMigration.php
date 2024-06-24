<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\DI\ContainerWrapper;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;

abstract class DbMigration {
  /** @var Connection */
  protected $connection;

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    ContainerWrapper $container
  ) {
    $this->connection = $container->get(Connection::class);
    $this->entityManager = $container->get(EntityManager::class);
  }

  abstract public function run(): void;

  /**
   * @param class-string<object> $entityClass
   */
  protected function getTableName(string $entityClass): string {
    return $this->entityManager->getClassMetadata($entityClass)->getTableName();
  }

  protected function createTable(string $tableName, array $attributes): void {
    $prefix = Env::$dbPrefix;
    $charsetCollate = Env::$dbCharsetCollate;
    $sql = implode(",\n", $attributes);
    $this->connection->executeStatement("
      CREATE TABLE IF NOT EXISTS {$prefix}{$tableName} (
        $sql
      ) {$charsetCollate};
    ");
  }

  protected function columnExists(string $tableName, string $columnName): bool {
    // We had a problem with the dbName value in ENV for some customers, because it doesn't match DB name in information schema.
    // So we decided to use the DATABASE() value instead.
    return $this->connection->executeQuery("
      SELECT 1
      FROM information_schema.columns
      WHERE table_schema = COALESCE(DATABASE(), ?)
      AND table_name = ?
      AND column_name = ?
    ", [Env::$dbName, $tableName, $columnName])->fetchOne() !== false;
  }

  protected function tableExists(string $tableName): bool {
    return $this->connection->executeQuery("
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = COALESCE(DATABASE(), ?)
        AND table_name = ?
      ", [Env::$dbName, $tableName])->fetchOne() !== false;
  }

  protected function indexExists(string $tableName, string $indexName): bool {
    // We had a problem with the dbName value in ENV for some customers, because it doesn't match DB name in information schema.
    // So we decided to use the DATABASE() value instead.
    return $this->connection->executeQuery("
      SELECT 1
      FROM information_schema.statistics
      WHERE table_schema = COALESCE(DATABASE(), ?)
      AND table_name = ?
      AND index_name = ?
    ", [Env::$dbName, $tableName, $indexName])->fetchOne() !== false;
  }
}
