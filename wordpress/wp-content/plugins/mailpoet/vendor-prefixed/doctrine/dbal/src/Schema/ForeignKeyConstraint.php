<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function array_keys;
use function array_map;
use function strrpos;
use function strtolower;
use function strtoupper;
use function substr;
class ForeignKeyConstraint extends AbstractAsset implements Constraint
{
 protected $_localTable;
 protected $_localColumnNames;
 protected $_foreignTableName;
 protected $_foreignColumnNames;
 protected $_options;
 public function __construct(array $localColumnNames, $foreignTableName, array $foreignColumnNames, $name = null, array $options = [])
 {
 if ($name !== null) {
 $this->_setName($name);
 }
 $this->_localColumnNames = $this->createIdentifierMap($localColumnNames);
 if ($foreignTableName instanceof Table) {
 $this->_foreignTableName = $foreignTableName;
 } else {
 $this->_foreignTableName = new Identifier($foreignTableName);
 }
 $this->_foreignColumnNames = $this->createIdentifierMap($foreignColumnNames);
 $this->_options = $options;
 }
 private function createIdentifierMap(array $names) : array
 {
 $identifiers = [];
 foreach ($names as $name) {
 $identifiers[$name] = new Identifier($name);
 }
 return $identifiers;
 }
 public function getLocalTableName()
 {
 return $this->_localTable->getName();
 }
 public function setLocalTable(Table $table)
 {
 $this->_localTable = $table;
 }
 public function getLocalTable()
 {
 return $this->_localTable;
 }
 public function getLocalColumns()
 {
 return array_keys($this->_localColumnNames);
 }
 public function getQuotedLocalColumns(AbstractPlatform $platform)
 {
 $columns = [];
 foreach ($this->_localColumnNames as $column) {
 $columns[] = $column->getQuotedName($platform);
 }
 return $columns;
 }
 public function getUnquotedLocalColumns()
 {
 return array_map([$this, 'trimQuotes'], $this->getLocalColumns());
 }
 public function getUnquotedForeignColumns()
 {
 return array_map([$this, 'trimQuotes'], $this->getForeignColumns());
 }
 public function getColumns()
 {
 return $this->getLocalColumns();
 }
 public function getQuotedColumns(AbstractPlatform $platform)
 {
 return $this->getQuotedLocalColumns($platform);
 }
 public function getForeignTableName()
 {
 return $this->_foreignTableName->getName();
 }
 public function getUnqualifiedForeignTableName()
 {
 $name = $this->_foreignTableName->getName();
 $position = strrpos($name, '.');
 if ($position !== \false) {
 $name = substr($name, $position + 1);
 }
 return strtolower($name);
 }
 public function getQuotedForeignTableName(AbstractPlatform $platform)
 {
 return $this->_foreignTableName->getQuotedName($platform);
 }
 public function getForeignColumns()
 {
 return array_keys($this->_foreignColumnNames);
 }
 public function getQuotedForeignColumns(AbstractPlatform $platform)
 {
 $columns = [];
 foreach ($this->_foreignColumnNames as $column) {
 $columns[] = $column->getQuotedName($platform);
 }
 return $columns;
 }
 public function hasOption($name)
 {
 return isset($this->_options[$name]);
 }
 public function getOption($name)
 {
 return $this->_options[$name];
 }
 public function getOptions()
 {
 return $this->_options;
 }
 public function onUpdate()
 {
 return $this->onEvent('onUpdate');
 }
 public function onDelete()
 {
 return $this->onEvent('onDelete');
 }
 private function onEvent($event) : ?string
 {
 if (isset($this->_options[$event])) {
 $onEvent = strtoupper($this->_options[$event]);
 if ($onEvent !== 'NO ACTION' && $onEvent !== 'RESTRICT') {
 return $onEvent;
 }
 }
 return null;
 }
 public function intersectsIndexColumns(Index $index)
 {
 foreach ($index->getColumns() as $indexColumn) {
 foreach ($this->_localColumnNames as $localColumn) {
 if (strtolower($indexColumn) === strtolower($localColumn->getName())) {
 return \true;
 }
 }
 }
 return \false;
 }
}
