<?php
namespace MailPoetVendor\Doctrine\DBAL\Cache;
if (!defined('ABSPATH')) exit;
use ArrayIterator;
use MailPoetVendor\Doctrine\DBAL\Driver\FetchUtils;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
use MailPoetVendor\Doctrine\DBAL\Driver\ResultStatement;
use MailPoetVendor\Doctrine\DBAL\FetchMode;
use InvalidArgumentException;
use IteratorAggregate;
use PDO;
use ReturnTypeWillChange;
use function array_merge;
use function array_values;
use function count;
use function reset;
class ArrayStatement implements IteratorAggregate, ResultStatement, Result
{
 private $data;
 private $columnCount = 0;
 private $num = 0;
 private $defaultFetchMode = FetchMode::MIXED;
 public function __construct(array $data)
 {
 $this->data = $data;
 if (!count($data)) {
 return;
 }
 $this->columnCount = count($data[0]);
 }
 public function closeCursor()
 {
 $this->free();
 return \true;
 }
 public function rowCount()
 {
 return count($this->data);
 }
 public function columnCount()
 {
 return $this->columnCount;
 }
 public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
 {
 if ($arg2 !== null || $arg3 !== null) {
 throw new InvalidArgumentException('Caching layer does not support 2nd/3rd argument to setFetchMode()');
 }
 $this->defaultFetchMode = $fetchMode;
 return \true;
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 $data = $this->fetchAll();
 return new ArrayIterator($data);
 }
 public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
 {
 if (!isset($this->data[$this->num])) {
 return \false;
 }
 $row = $this->data[$this->num++];
 $fetchMode = $fetchMode ?: $this->defaultFetchMode;
 if ($fetchMode === FetchMode::ASSOCIATIVE) {
 return $row;
 }
 if ($fetchMode === FetchMode::NUMERIC) {
 return array_values($row);
 }
 if ($fetchMode === FetchMode::MIXED) {
 return array_merge($row, array_values($row));
 }
 if ($fetchMode === FetchMode::COLUMN) {
 return reset($row);
 }
 throw new InvalidArgumentException('Invalid fetch-style given for fetching result.');
 }
 public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
 {
 $rows = [];
 while ($row = $this->fetch($fetchMode)) {
 $rows[] = $row;
 }
 return $rows;
 }
 public function fetchColumn($columnIndex = 0)
 {
 $row = $this->fetch(FetchMode::NUMERIC);
 // TODO: verify that return false is the correct behavior
 return $row[$columnIndex] ?? \false;
 }
 public function fetchNumeric()
 {
 $row = $this->doFetch();
 if ($row === \false) {
 return \false;
 }
 return array_values($row);
 }
 public function fetchAssociative()
 {
 return $this->doFetch();
 }
 public function fetchOne()
 {
 $row = $this->doFetch();
 if ($row === \false) {
 return \false;
 }
 return reset($row);
 }
 public function fetchAllNumeric() : array
 {
 return FetchUtils::fetchAllNumeric($this);
 }
 public function fetchAllAssociative() : array
 {
 return FetchUtils::fetchAllAssociative($this);
 }
 public function fetchFirstColumn() : array
 {
 return FetchUtils::fetchFirstColumn($this);
 }
 public function free() : void
 {
 $this->data = [];
 }
 private function doFetch()
 {
 if (!isset($this->data[$this->num])) {
 return \false;
 }
 return $this->data[$this->num++];
 }
}
