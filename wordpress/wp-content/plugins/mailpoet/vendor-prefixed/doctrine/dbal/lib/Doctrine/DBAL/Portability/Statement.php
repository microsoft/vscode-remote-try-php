<?php
namespace MailPoetVendor\Doctrine\DBAL\Portability;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
use MailPoetVendor\Doctrine\DBAL\Driver\ResultStatement;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement as DriverStatement;
use MailPoetVendor\Doctrine\DBAL\Driver\StatementIterator;
use MailPoetVendor\Doctrine\DBAL\FetchMode;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use IteratorAggregate;
use PDO;
use ReturnTypeWillChange;
use function array_change_key_case;
use function assert;
use function is_string;
use function rtrim;
class Statement implements IteratorAggregate, DriverStatement, Result
{
 private $portability;
 private $stmt;
 private $case;
 private $defaultFetchMode = FetchMode::MIXED;
 public function __construct($stmt, Connection $conn)
 {
 $this->stmt = $stmt;
 $this->portability = $conn->getPortability();
 $this->case = $conn->getFetchCase();
 }
 public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
 {
 assert($this->stmt instanceof DriverStatement);
 return $this->stmt->bindParam($param, $variable, $type, $length);
 }
 public function bindValue($param, $value, $type = ParameterType::STRING)
 {
 assert($this->stmt instanceof DriverStatement);
 return $this->stmt->bindValue($param, $value, $type);
 }
 public function closeCursor()
 {
 return $this->stmt->closeCursor();
 }
 public function columnCount()
 {
 return $this->stmt->columnCount();
 }
 public function errorCode()
 {
 assert($this->stmt instanceof DriverStatement);
 return $this->stmt->errorCode();
 }
 public function errorInfo()
 {
 assert($this->stmt instanceof DriverStatement);
 return $this->stmt->errorInfo();
 }
 public function execute($params = null)
 {
 assert($this->stmt instanceof DriverStatement);
 return $this->stmt->execute($params);
 }
 public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
 {
 $this->defaultFetchMode = $fetchMode;
 return $this->stmt->setFetchMode($fetchMode, $arg2, $arg3);
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 return new StatementIterator($this);
 }
 public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
 {
 $fetchMode = $fetchMode ?: $this->defaultFetchMode;
 $row = $this->stmt->fetch($fetchMode);
 $iterateRow = ($this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL | Connection::PORTABILITY_RTRIM)) !== 0;
 $fixCase = $this->case !== null && ($fetchMode === FetchMode::ASSOCIATIVE || $fetchMode === FetchMode::MIXED) && $this->portability & Connection::PORTABILITY_FIX_CASE;
 $row = $this->fixRow($row, $iterateRow, $fixCase);
 return $row;
 }
 public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
 {
 $fetchMode = $fetchMode ?: $this->defaultFetchMode;
 if ($fetchArgument) {
 $rows = $this->stmt->fetchAll($fetchMode, $fetchArgument);
 } else {
 $rows = $this->stmt->fetchAll($fetchMode);
 }
 $fixCase = $this->case !== null && ($fetchMode === FetchMode::ASSOCIATIVE || $fetchMode === FetchMode::MIXED) && $this->portability & Connection::PORTABILITY_FIX_CASE;
 return $this->fixResultSet($rows, $fixCase, $fetchMode !== FetchMode::COLUMN);
 }
 public function fetchNumeric()
 {
 if ($this->stmt instanceof Result) {
 $row = $this->stmt->fetchNumeric();
 } else {
 $row = $this->stmt->fetch(FetchMode::NUMERIC);
 }
 return $this->fixResult($row, \false);
 }
 public function fetchAssociative()
 {
 if ($this->stmt instanceof Result) {
 $row = $this->stmt->fetchAssociative();
 } else {
 $row = $this->stmt->fetch(FetchMode::ASSOCIATIVE);
 }
 return $this->fixResult($row, \true);
 }
 public function fetchOne()
 {
 if ($this->stmt instanceof Result) {
 $value = $this->stmt->fetchOne();
 } else {
 $value = $this->stmt->fetch(FetchMode::COLUMN);
 }
 if (($this->portability & Connection::PORTABILITY_EMPTY_TO_NULL) !== 0 && $value === '') {
 $value = null;
 } elseif (($this->portability & Connection::PORTABILITY_RTRIM) !== 0 && is_string($value)) {
 $value = rtrim($value);
 }
 return $value;
 }
 public function fetchAllNumeric() : array
 {
 if ($this->stmt instanceof Result) {
 $data = $this->stmt->fetchAllNumeric();
 } else {
 $data = $this->stmt->fetchAll(FetchMode::NUMERIC);
 }
 return $this->fixResultSet($data, \false, \true);
 }
 public function fetchAllAssociative() : array
 {
 if ($this->stmt instanceof Result) {
 $data = $this->stmt->fetchAllAssociative();
 } else {
 $data = $this->stmt->fetchAll(FetchMode::ASSOCIATIVE);
 }
 return $this->fixResultSet($data, \true, \true);
 }
 public function fetchFirstColumn() : array
 {
 if ($this->stmt instanceof Result) {
 $data = $this->stmt->fetchFirstColumn();
 } else {
 $data = $this->stmt->fetchAll(FetchMode::COLUMN);
 }
 return $this->fixResultSet($data, \true, \false);
 }
 public function free() : void
 {
 if ($this->stmt instanceof Result) {
 $this->stmt->free();
 return;
 }
 $this->stmt->closeCursor();
 }
 private function fixResult($result, bool $fixCase)
 {
 $iterateRow = ($this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL | Connection::PORTABILITY_RTRIM)) !== 0;
 $fixCase = $fixCase && $this->case !== null && ($this->portability & Connection::PORTABILITY_FIX_CASE) !== 0;
 return $this->fixRow($result, $iterateRow, $fixCase);
 }
 private function fixResultSet(array $resultSet, bool $fixCase, bool $isArray) : array
 {
 $iterateRow = ($this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL | Connection::PORTABILITY_RTRIM)) !== 0;
 $fixCase = $fixCase && $this->case !== null && ($this->portability & Connection::PORTABILITY_FIX_CASE) !== 0;
 if (!$iterateRow && !$fixCase) {
 return $resultSet;
 }
 if (!$isArray) {
 foreach ($resultSet as $num => $value) {
 $resultSet[$num] = [$value];
 }
 }
 foreach ($resultSet as $num => $row) {
 $resultSet[$num] = $this->fixRow($row, $iterateRow, $fixCase);
 }
 if (!$isArray) {
 foreach ($resultSet as $num => $row) {
 $resultSet[$num] = $row[0];
 }
 }
 return $resultSet;
 }
 protected function fixRow($row, $iterateRow, $fixCase)
 {
 if (!$row) {
 return $row;
 }
 if ($fixCase) {
 assert($this->case !== null);
 $row = array_change_key_case($row, $this->case);
 }
 if ($iterateRow) {
 foreach ($row as $k => $v) {
 if ($this->portability & Connection::PORTABILITY_EMPTY_TO_NULL && $v === '') {
 $row[$k] = null;
 } elseif ($this->portability & Connection::PORTABILITY_RTRIM && is_string($v)) {
 $row[$k] = rtrim($v);
 }
 }
 }
 return $row;
 }
 public function fetchColumn($columnIndex = 0)
 {
 $value = $this->stmt->fetchColumn($columnIndex);
 if ($this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL | Connection::PORTABILITY_RTRIM)) {
 if ($this->portability & Connection::PORTABILITY_EMPTY_TO_NULL && $value === '') {
 $value = null;
 } elseif ($this->portability & Connection::PORTABILITY_RTRIM && is_string($value)) {
 $value = rtrim($value);
 }
 }
 return $value;
 }
 public function rowCount()
 {
 assert($this->stmt instanceof DriverStatement);
 return $this->stmt->rowCount();
 }
}
