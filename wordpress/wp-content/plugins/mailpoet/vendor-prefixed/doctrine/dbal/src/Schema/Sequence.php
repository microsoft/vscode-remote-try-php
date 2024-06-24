<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\Visitor\Visitor;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function count;
use function sprintf;
class Sequence extends AbstractAsset
{
 protected $allocationSize = 1;
 protected $initialValue = 1;
 protected $cache;
 public function __construct($name, $allocationSize = 1, $initialValue = 1, $cache = null)
 {
 $this->_setName($name);
 $this->setAllocationSize($allocationSize);
 $this->setInitialValue($initialValue);
 $this->cache = $cache;
 }
 public function getAllocationSize()
 {
 return $this->allocationSize;
 }
 public function getInitialValue()
 {
 return $this->initialValue;
 }
 public function getCache()
 {
 return $this->cache;
 }
 public function setAllocationSize($allocationSize)
 {
 if ($allocationSize > 0) {
 $this->allocationSize = $allocationSize;
 } else {
 $this->allocationSize = 1;
 }
 return $this;
 }
 public function setInitialValue($initialValue)
 {
 if ($initialValue > 0) {
 $this->initialValue = $initialValue;
 } else {
 $this->initialValue = 1;
 }
 return $this;
 }
 public function setCache($cache)
 {
 $this->cache = $cache;
 return $this;
 }
 public function isAutoIncrementsFor(Table $table)
 {
 $primaryKey = $table->getPrimaryKey();
 if ($primaryKey === null) {
 return \false;
 }
 $pkColumns = $primaryKey->getColumns();
 if (count($pkColumns) !== 1) {
 return \false;
 }
 $column = $table->getColumn($pkColumns[0]);
 if (!$column->getAutoincrement()) {
 return \false;
 }
 $sequenceName = $this->getShortestName($table->getNamespaceName());
 $tableName = $table->getShortestName($table->getNamespaceName());
 $tableSequenceName = sprintf('%s_%s_seq', $tableName, $column->getShortestName($table->getNamespaceName()));
 return $tableSequenceName === $sequenceName;
 }
 public function visit(Visitor $visitor)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5435', 'Sequence::visit() is deprecated.');
 $visitor->acceptSequence($this);
 }
}
