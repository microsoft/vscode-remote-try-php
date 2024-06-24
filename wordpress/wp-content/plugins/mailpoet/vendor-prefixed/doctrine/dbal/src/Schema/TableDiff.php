<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_filter;
use function array_values;
use function count;
class TableDiff
{
 public $name;
 public $newName = \false;
 public $addedColumns;
 public $changedColumns = [];
 public $removedColumns = [];
 public $renamedColumns = [];
 public $addedIndexes = [];
 public $changedIndexes = [];
 public $removedIndexes = [];
 public $renamedIndexes = [];
 public $addedForeignKeys = [];
 public $changedForeignKeys = [];
 public $removedForeignKeys = [];
 public $fromTable;
 public function __construct($tableName, $addedColumns = [], $modifiedColumns = [], $droppedColumns = [], $addedIndexes = [], $changedIndexes = [], $removedIndexes = [], ?Table $fromTable = null, $addedForeignKeys = [], $changedForeignKeys = [], $removedForeignKeys = [], $renamedColumns = [], $renamedIndexes = [])
 {
 $this->name = $tableName;
 $this->addedColumns = $addedColumns;
 $this->changedColumns = $modifiedColumns;
 $this->renamedColumns = $renamedColumns;
 $this->removedColumns = $droppedColumns;
 $this->addedIndexes = $addedIndexes;
 $this->changedIndexes = $changedIndexes;
 $this->renamedIndexes = $renamedIndexes;
 $this->removedIndexes = $removedIndexes;
 $this->addedForeignKeys = $addedForeignKeys;
 $this->changedForeignKeys = $changedForeignKeys;
 $this->removedForeignKeys = $removedForeignKeys;
 if ($fromTable === null) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5678', 'Not passing the $fromTable to %s is deprecated.', __METHOD__);
 }
 $this->fromTable = $fromTable;
 }
 public function getName(AbstractPlatform $platform)
 {
 return new Identifier($this->fromTable instanceof Table ? $this->fromTable->getQuotedName($platform) : $this->name);
 }
 public function getNewName()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5663', '%s is deprecated. Rename tables via AbstractSchemaManager::renameTable() instead.', __METHOD__);
 if ($this->newName === \false) {
 return \false;
 }
 return new Identifier($this->newName);
 }
 public function getOldTable() : ?Table
 {
 return $this->fromTable;
 }
 public function getAddedColumns() : array
 {
 return array_values($this->addedColumns);
 }
 public function getModifiedColumns() : array
 {
 return array_values($this->changedColumns);
 }
 public function getDroppedColumns() : array
 {
 return array_values($this->removedColumns);
 }
 public function getRenamedColumns() : array
 {
 return $this->renamedColumns;
 }
 public function getAddedIndexes() : array
 {
 return array_values($this->addedIndexes);
 }
 public function unsetAddedIndex(Index $index) : void
 {
 $this->addedIndexes = array_filter($this->addedIndexes, static function (Index $addedIndex) use($index) : bool {
 return $addedIndex !== $index;
 });
 }
 public function getModifiedIndexes() : array
 {
 return array_values($this->changedIndexes);
 }
 public function getDroppedIndexes() : array
 {
 return array_values($this->removedIndexes);
 }
 public function unsetDroppedIndex(Index $index) : void
 {
 $this->removedIndexes = array_filter($this->removedIndexes, static function (Index $removedIndex) use($index) : bool {
 return $removedIndex !== $index;
 });
 }
 public function getRenamedIndexes() : array
 {
 return $this->renamedIndexes;
 }
 public function getAddedForeignKeys() : array
 {
 return $this->addedForeignKeys;
 }
 public function getModifiedForeignKeys() : array
 {
 return $this->changedForeignKeys;
 }
 public function getDroppedForeignKeys() : array
 {
 return $this->removedForeignKeys;
 }
 public function unsetDroppedForeignKey($foreignKey) : void
 {
 $this->removedForeignKeys = array_filter($this->removedForeignKeys, static function ($removedForeignKey) use($foreignKey) : bool {
 return $removedForeignKey !== $foreignKey;
 });
 }
 public function isEmpty() : bool
 {
 return count($this->addedColumns) === 0 && count($this->changedColumns) === 0 && count($this->removedColumns) === 0 && count($this->renamedColumns) === 0 && count($this->addedIndexes) === 0 && count($this->changedIndexes) === 0 && count($this->removedIndexes) === 0 && count($this->renamedIndexes) === 0 && count($this->addedForeignKeys) === 0 && count($this->changedForeignKeys) === 0 && count($this->removedForeignKeys) === 0;
 }
}
