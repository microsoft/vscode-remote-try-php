<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\SQLite3;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\FetchUtils;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as ResultInterface;
use SQLite3Result;
use const SQLITE3_ASSOC;
use const SQLITE3_NUM;
final class Result implements ResultInterface
{
 private ?SQLite3Result $result;
 private int $changes;
 public function __construct(SQLite3Result $result, int $changes)
 {
 $this->result = $result;
 $this->changes = $changes;
 }
 public function fetchNumeric()
 {
 if ($this->result === null) {
 return \false;
 }
 return $this->result->fetchArray(SQLITE3_NUM);
 }
 public function fetchAssociative()
 {
 if ($this->result === null) {
 return \false;
 }
 return $this->result->fetchArray(SQLITE3_ASSOC);
 }
 public function fetchOne()
 {
 return FetchUtils::fetchOne($this);
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
 public function rowCount() : int
 {
 return $this->changes;
 }
 public function columnCount() : int
 {
 if ($this->result === null) {
 return 0;
 }
 return $this->result->numColumns();
 }
 public function free() : void
 {
 if ($this->result === null) {
 return;
 }
 $this->result->finalize();
 $this->result = null;
 }
}
