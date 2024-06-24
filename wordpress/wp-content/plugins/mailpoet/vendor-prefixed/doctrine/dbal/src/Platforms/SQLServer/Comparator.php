<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\SQLServer;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\SQLServerPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\Comparator as BaseComparator;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
class Comparator extends BaseComparator
{
 private string $databaseCollation;
 public function __construct(SQLServerPlatform $platform, string $databaseCollation)
 {
 parent::__construct($platform);
 $this->databaseCollation = $databaseCollation;
 }
 public function compareTables(Table $fromTable, Table $toTable) : TableDiff
 {
 return parent::compareTables($this->normalizeColumns($fromTable), $this->normalizeColumns($toTable));
 }
 public function diffTable(Table $fromTable, Table $toTable)
 {
 return parent::diffTable($this->normalizeColumns($fromTable), $this->normalizeColumns($toTable));
 }
 private function normalizeColumns(Table $table) : Table
 {
 $table = clone $table;
 foreach ($table->getColumns() as $column) {
 $options = $column->getPlatformOptions();
 if (!isset($options['collation']) || $options['collation'] !== $this->databaseCollation) {
 continue;
 }
 unset($options['collation']);
 $column->setPlatformOptions($options);
 }
 return $table;
 }
}
