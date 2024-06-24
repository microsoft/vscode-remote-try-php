<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
class TableGenerator extends AbstractIdGenerator
{
 private $_tableName;
 private $_sequenceName;
 private $_allocationSize;
 private $_nextValue;
 private $_maxValue;
 public function __construct($tableName, $sequenceName = 'default', $allocationSize = 10)
 {
 $this->_tableName = $tableName;
 $this->_sequenceName = $sequenceName;
 $this->_allocationSize = $allocationSize;
 }
 public function generateId(EntityManagerInterface $em, $entity)
 {
 if ($this->_maxValue === null || $this->_nextValue === $this->_maxValue) {
 // Allocate new values
 $conn = $em->getConnection();
 if ($conn->getTransactionNestingLevel() === 0) {
 // use select for update
 $sql = $conn->getDatabasePlatform()->getTableHiLoCurrentValSql($this->_tableName, $this->_sequenceName);
 $currentLevel = $conn->fetchOne($sql);
 if ($currentLevel !== null) {
 $this->_nextValue = $currentLevel;
 $this->_maxValue = $this->_nextValue + $this->_allocationSize;
 $updateSql = $conn->getDatabasePlatform()->getTableHiLoUpdateNextValSql($this->_tableName, $this->_sequenceName, $this->_allocationSize);
 if ($conn->executeStatement($updateSql, [1 => $currentLevel, 2 => $currentLevel + 1]) !== 1) {
 // no affected rows, concurrency issue, throw exception
 }
 } else {
 // no current level returned, TableGenerator seems to be broken, throw exception
 }
 } else {
 // only table locks help here, implement this or throw exception?
 // or do we want to work with table locks exclusively?
 }
 }
 return $this->_nextValue++;
 }
}
