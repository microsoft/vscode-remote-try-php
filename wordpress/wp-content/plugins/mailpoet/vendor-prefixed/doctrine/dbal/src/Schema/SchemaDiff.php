<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_filter;
use function array_merge;
use function count;
class SchemaDiff
{
 public $fromSchema;
 public $newNamespaces = [];
 public $removedNamespaces = [];
 public $newTables = [];
 public $changedTables = [];
 public $removedTables = [];
 public $newSequences = [];
 public $changedSequences = [];
 public $removedSequences = [];
 public $orphanedForeignKeys = [];
 public function __construct($newTables = [], $changedTables = [], $removedTables = [], ?Schema $fromSchema = null, $createdSchemas = [], $droppedSchemas = [], $createdSequences = [], $alteredSequences = [], $droppedSequences = [])
 {
 $this->newTables = $newTables;
 $this->changedTables = array_filter($changedTables, static function (TableDiff $diff) : bool {
 return !$diff->isEmpty();
 });
 $this->removedTables = $removedTables;
 $this->fromSchema = $fromSchema;
 $this->newNamespaces = $createdSchemas;
 $this->removedNamespaces = $droppedSchemas;
 $this->newSequences = $createdSequences;
 $this->changedSequences = $alteredSequences;
 $this->removedSequences = $droppedSequences;
 }
 public function getCreatedSchemas() : array
 {
 return $this->newNamespaces;
 }
 public function getDroppedSchemas() : array
 {
 return $this->removedNamespaces;
 }
 public function getCreatedTables() : array
 {
 return $this->newTables;
 }
 public function getAlteredTables() : array
 {
 return $this->changedTables;
 }
 public function getDroppedTables() : array
 {
 return $this->removedTables;
 }
 public function getCreatedSequences() : array
 {
 return $this->newSequences;
 }
 public function getAlteredSequences() : array
 {
 return $this->changedSequences;
 }
 public function getDroppedSequences() : array
 {
 return $this->removedSequences;
 }
 public function isEmpty() : bool
 {
 return count($this->newNamespaces) === 0 && count($this->removedNamespaces) === 0 && count($this->newTables) === 0 && count($this->changedTables) === 0 && count($this->removedTables) === 0 && count($this->newSequences) === 0 && count($this->changedSequences) === 0 && count($this->removedSequences) === 0;
 }
 public function toSaveSql(AbstractPlatform $platform)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5766', '%s is deprecated.', __METHOD__);
 return $this->_toSql($platform, \true);
 }
 public function toSql(AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5766', '%s is deprecated. Use AbstractPlatform::getAlterSchemaSQL() instead.', __METHOD__);
 return $this->_toSql($platform, \false);
 }
 protected function _toSql(AbstractPlatform $platform, $saveMode = \false)
 {
 $sql = [];
 if ($platform->supportsSchemas()) {
 foreach ($this->getCreatedSchemas() as $schema) {
 $sql[] = $platform->getCreateSchemaSQL($schema);
 }
 }
 if ($platform->supportsForeignKeyConstraints() && $saveMode === \false) {
 foreach ($this->orphanedForeignKeys as $orphanedForeignKey) {
 $sql[] = $platform->getDropForeignKeySQL($orphanedForeignKey, $orphanedForeignKey->getLocalTable());
 }
 }
 if ($platform->supportsSequences() === \true) {
 foreach ($this->getAlteredSequences() as $sequence) {
 $sql[] = $platform->getAlterSequenceSQL($sequence);
 }
 if ($saveMode === \false) {
 foreach ($this->getDroppedSequences() as $sequence) {
 $sql[] = $platform->getDropSequenceSQL($sequence);
 }
 }
 foreach ($this->getCreatedSequences() as $sequence) {
 $sql[] = $platform->getCreateSequenceSQL($sequence);
 }
 }
 $sql = array_merge($sql, $platform->getCreateTablesSQL($this->getCreatedTables()));
 if ($saveMode === \false) {
 $sql = array_merge($sql, $platform->getDropTablesSQL($this->getDroppedTables()));
 }
 foreach ($this->getAlteredTables() as $tableDiff) {
 $sql = array_merge($sql, $platform->getAlterTableSQL($tableDiff));
 }
 return $sql;
 }
}
