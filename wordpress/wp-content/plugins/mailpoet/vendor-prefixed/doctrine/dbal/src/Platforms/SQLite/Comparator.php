<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\SQLite;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\SqlitePlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\Comparator as BaseComparator;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
use function strcasecmp;
class Comparator extends BaseComparator
{
 public function __construct(SqlitePlatform $platform)
 {
 parent::__construct($platform);
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
 if (!isset($options['collation']) || strcasecmp($options['collation'], 'binary') !== 0) {
 continue;
 }
 unset($options['collation']);
 $column->setPlatformOptions($options);
 }
 return $table;
 }
}
