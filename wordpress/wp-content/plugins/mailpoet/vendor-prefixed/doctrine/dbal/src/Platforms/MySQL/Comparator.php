<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\MySQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\Comparator as BaseComparator;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
use function array_diff_assoc;
use function array_intersect_key;
class Comparator extends BaseComparator
{
 private $collationMetadataProvider;
 public function __construct(AbstractMySQLPlatform $platform, CollationMetadataProvider $collationMetadataProvider)
 {
 parent::__construct($platform);
 $this->collationMetadataProvider = $collationMetadataProvider;
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
 $tableOptions = array_intersect_key($table->getOptions(), ['charset' => null, 'collation' => null]);
 $table = clone $table;
 foreach ($table->getColumns() as $column) {
 $originalOptions = $column->getPlatformOptions();
 $normalizedOptions = $this->normalizeOptions($originalOptions);
 $overrideOptions = array_diff_assoc($normalizedOptions, $tableOptions);
 if ($overrideOptions === $originalOptions) {
 continue;
 }
 $column->setPlatformOptions($overrideOptions);
 }
 return $table;
 }
 private function normalizeOptions(array $options) : array
 {
 if (isset($options['collation']) && !isset($options['charset'])) {
 $charset = $this->collationMetadataProvider->getCollationCharset($options['collation']);
 if ($charset !== null) {
 $options['charset'] = $charset;
 }
 }
 return $options;
 }
}
