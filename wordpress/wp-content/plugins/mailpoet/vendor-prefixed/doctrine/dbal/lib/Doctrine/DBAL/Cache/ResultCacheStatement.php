<?php
namespace MailPoetVendor\Doctrine\DBAL\Cache;
if (!defined('ABSPATH')) exit;
use ArrayIterator;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Driver\FetchUtils;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
use MailPoetVendor\Doctrine\DBAL\Driver\ResultStatement;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement;
use MailPoetVendor\Doctrine\DBAL\FetchMode;
use InvalidArgumentException;
use IteratorAggregate;
use PDO;
use ReturnTypeWillChange;
use function array_map;
use function array_merge;
use function array_values;
use function assert;
use function reset;
class ResultCacheStatement implements IteratorAggregate, ResultStatement, Result
{
 private $resultCache;
 private $cacheKey;
 private $realKey;
 private $lifetime;
 private $statement;
 private $data;
 private $defaultFetchMode = FetchMode::MIXED;
 public function __construct(ResultStatement $stmt, Cache $resultCache, $cacheKey, $realKey, $lifetime)
 {
 $this->statement = $stmt;
 $this->resultCache = $resultCache;
 $this->cacheKey = $cacheKey;
 $this->realKey = $realKey;
 $this->lifetime = $lifetime;
 }
 public function closeCursor()
 {
 $this->free();
 return \true;
 }
 public function columnCount()
 {
 return $this->statement->columnCount();
 }
 public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
 {
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
 if ($this->data === null) {
 $this->data = [];
 }
 $row = $this->statement->fetch(FetchMode::ASSOCIATIVE);
 if ($row) {
 $this->data[] = $row;
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
 throw new InvalidArgumentException('Invalid fetch-style given for caching result.');
 }
 $this->saveToCache();
 return \false;
 }
 public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
 {
 $data = $this->statement->fetchAll(FetchMode::ASSOCIATIVE, $fetchArgument, $ctorArgs);
 $this->data = $data;
 $this->saveToCache();
 if ($fetchMode === FetchMode::NUMERIC) {
 foreach ($data as $i => $row) {
 $data[$i] = array_values($row);
 }
 } elseif ($fetchMode === FetchMode::MIXED) {
 foreach ($data as $i => $row) {
 $data[$i] = array_merge($row, array_values($row));
 }
 } elseif ($fetchMode === FetchMode::COLUMN) {
 foreach ($data as $i => $row) {
 $data[$i] = reset($row);
 }
 }
 return $data;
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
 return FetchUtils::fetchOne($this);
 }
 public function fetchAllNumeric() : array
 {
 if ($this->statement instanceof Result) {
 $data = $this->statement->fetchAllAssociative();
 } else {
 $data = $this->statement->fetchAll(FetchMode::ASSOCIATIVE);
 }
 $this->data = $data;
 $this->saveToCache();
 return array_map('array_values', $data);
 }
 public function fetchAllAssociative() : array
 {
 if ($this->statement instanceof Result) {
 $data = $this->statement->fetchAllAssociative();
 } else {
 $data = $this->statement->fetchAll(FetchMode::ASSOCIATIVE);
 }
 $this->data = $data;
 $this->saveToCache();
 return $data;
 }
 public function fetchFirstColumn() : array
 {
 return FetchUtils::fetchFirstColumn($this);
 }
 public function rowCount()
 {
 assert($this->statement instanceof Statement);
 return $this->statement->rowCount();
 }
 public function free() : void
 {
 $this->data = null;
 }
 private function doFetch()
 {
 if ($this->data === null) {
 $this->data = [];
 }
 if ($this->statement instanceof Result) {
 $row = $this->statement->fetchAssociative();
 } else {
 $row = $this->statement->fetch(FetchMode::ASSOCIATIVE);
 }
 if ($row !== \false) {
 $this->data[] = $row;
 return $row;
 }
 $this->saveToCache();
 return \false;
 }
 private function saveToCache() : void
 {
 if ($this->data === null) {
 return;
 }
 $data = $this->resultCache->fetch($this->cacheKey);
 if (!$data) {
 $data = [];
 }
 $data[$this->realKey] = $this->data;
 $this->resultCache->save($this->cacheKey, $data, $this->lifetime);
 }
}
