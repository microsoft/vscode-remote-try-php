<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\Middleware;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
abstract class AbstractResultMiddleware implements Result
{
 private Result $wrappedResult;
 public function __construct(Result $result)
 {
 $this->wrappedResult = $result;
 }
 public function fetchNumeric()
 {
 return $this->wrappedResult->fetchNumeric();
 }
 public function fetchAssociative()
 {
 return $this->wrappedResult->fetchAssociative();
 }
 public function fetchOne()
 {
 return $this->wrappedResult->fetchOne();
 }
 public function fetchAllNumeric() : array
 {
 return $this->wrappedResult->fetchAllNumeric();
 }
 public function fetchAllAssociative() : array
 {
 return $this->wrappedResult->fetchAllAssociative();
 }
 public function fetchFirstColumn() : array
 {
 return $this->wrappedResult->fetchFirstColumn();
 }
 public function rowCount() : int
 {
 return $this->wrappedResult->rowCount();
 }
 public function columnCount() : int
 {
 return $this->wrappedResult->columnCount();
 }
 public function free() : void
 {
 $this->wrappedResult->free();
 }
}
