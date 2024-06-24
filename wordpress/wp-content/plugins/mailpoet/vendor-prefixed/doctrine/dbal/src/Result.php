<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception as DriverException;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as DriverResult;
use MailPoetVendor\Doctrine\DBAL\Exception\NoKeyValue;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use LogicException;
use Traversable;
use function array_shift;
use function func_num_args;
class Result
{
 private DriverResult $result;
 private Connection $connection;
 public function __construct(DriverResult $result, Connection $connection)
 {
 $this->result = $result;
 $this->connection = $connection;
 }
 public function fetchNumeric()
 {
 try {
 return $this->result->fetchNumeric();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function fetchAssociative()
 {
 try {
 return $this->result->fetchAssociative();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function fetchOne()
 {
 try {
 return $this->result->fetchOne();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function fetchAllNumeric() : array
 {
 try {
 return $this->result->fetchAllNumeric();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function fetchAllAssociative() : array
 {
 try {
 return $this->result->fetchAllAssociative();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function fetchAllKeyValue() : array
 {
 $this->ensureHasKeyValue();
 $data = [];
 foreach ($this->fetchAllNumeric() as [$key, $value]) {
 $data[$key] = $value;
 }
 return $data;
 }
 public function fetchAllAssociativeIndexed() : array
 {
 $data = [];
 foreach ($this->fetchAllAssociative() as $row) {
 $data[array_shift($row)] = $row;
 }
 return $data;
 }
 public function fetchFirstColumn() : array
 {
 try {
 return $this->result->fetchFirstColumn();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function iterateNumeric() : Traversable
 {
 while (($row = $this->fetchNumeric()) !== \false) {
 (yield $row);
 }
 }
 public function iterateAssociative() : Traversable
 {
 while (($row = $this->fetchAssociative()) !== \false) {
 (yield $row);
 }
 }
 public function iterateKeyValue() : Traversable
 {
 $this->ensureHasKeyValue();
 foreach ($this->iterateNumeric() as [$key, $value]) {
 (yield $key => $value);
 }
 }
 public function iterateAssociativeIndexed() : Traversable
 {
 foreach ($this->iterateAssociative() as $row) {
 (yield array_shift($row) => $row);
 }
 }
 public function iterateColumn() : Traversable
 {
 while (($value = $this->fetchOne()) !== \false) {
 (yield $value);
 }
 }
 public function rowCount() : int
 {
 try {
 return $this->result->rowCount();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function columnCount() : int
 {
 try {
 return $this->result->columnCount();
 } catch (DriverException $e) {
 throw $this->connection->convertException($e);
 }
 }
 public function free() : void
 {
 $this->result->free();
 }
 private function ensureHasKeyValue() : void
 {
 $columnCount = $this->columnCount();
 if ($columnCount < 2) {
 throw NoKeyValue::fromColumnCount($columnCount);
 }
 }
 public function fetch(int $mode = FetchMode::ASSOCIATIVE)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4007', '%s is deprecated, please use fetchNumeric(), fetchAssociative() or fetchOne() instead.', __METHOD__);
 if (func_num_args() > 1) {
 throw new LogicException('Only invocations with one argument are still supported by this legacy API.');
 }
 if ($mode === FetchMode::ASSOCIATIVE) {
 return $this->fetchAssociative();
 }
 if ($mode === FetchMode::NUMERIC) {
 return $this->fetchNumeric();
 }
 if ($mode === FetchMode::COLUMN) {
 return $this->fetchOne();
 }
 throw new LogicException('Only fetch modes declared on Doctrine\\DBAL\\FetchMode are supported by legacy API.');
 }
 public function fetchAll(int $mode = FetchMode::ASSOCIATIVE) : array
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4007', '%s is deprecated, please use fetchAllNumeric(), fetchAllAssociative() or fetchFirstColumn() instead.', __METHOD__);
 if (func_num_args() > 1) {
 throw new LogicException('Only invocations with one argument are still supported by this legacy API.');
 }
 if ($mode === FetchMode::ASSOCIATIVE) {
 return $this->fetchAllAssociative();
 }
 if ($mode === FetchMode::NUMERIC) {
 return $this->fetchAllNumeric();
 }
 if ($mode === FetchMode::COLUMN) {
 return $this->fetchFirstColumn();
 }
 throw new LogicException('Only fetch modes declared on Doctrine\\DBAL\\FetchMode are supported by legacy API.');
 }
}
