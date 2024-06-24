<?php
namespace MailPoetVendor\Doctrine\DBAL\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\FetchUtils;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
use function array_values;
use function count;
use function reset;
final class ArrayResult implements Result
{
 private array $data;
 private int $columnCount = 0;
 private int $num = 0;
 public function __construct(array $data)
 {
 $this->data = $data;
 if (count($data) === 0) {
 return;
 }
 $this->columnCount = count($data[0]);
 }
 public function fetchNumeric()
 {
 $row = $this->fetch();
 if ($row === \false) {
 return \false;
 }
 return array_values($row);
 }
 public function fetchAssociative()
 {
 return $this->fetch();
 }
 public function fetchOne()
 {
 $row = $this->fetch();
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
 public function rowCount() : int
 {
 return count($this->data);
 }
 public function columnCount() : int
 {
 return $this->columnCount;
 }
 public function free() : void
 {
 $this->data = [];
 }
 private function fetch()
 {
 if (!isset($this->data[$this->num])) {
 return \false;
 }
 return $this->data[$this->num++];
 }
}
