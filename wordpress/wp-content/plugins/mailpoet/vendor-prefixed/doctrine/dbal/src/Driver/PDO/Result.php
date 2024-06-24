<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as ResultInterface;
use PDO;
use PDOException;
use PDOStatement;
final class Result implements ResultInterface
{
 private PDOStatement $statement;
 public function __construct(PDOStatement $statement)
 {
 $this->statement = $statement;
 }
 public function fetchNumeric()
 {
 return $this->fetch(PDO::FETCH_NUM);
 }
 public function fetchAssociative()
 {
 return $this->fetch(PDO::FETCH_ASSOC);
 }
 public function fetchOne()
 {
 return $this->fetch(PDO::FETCH_COLUMN);
 }
 public function fetchAllNumeric() : array
 {
 return $this->fetchAll(PDO::FETCH_NUM);
 }
 public function fetchAllAssociative() : array
 {
 return $this->fetchAll(PDO::FETCH_ASSOC);
 }
 public function fetchFirstColumn() : array
 {
 return $this->fetchAll(PDO::FETCH_COLUMN);
 }
 public function rowCount() : int
 {
 try {
 return $this->statement->rowCount();
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 public function columnCount() : int
 {
 try {
 return $this->statement->columnCount();
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 public function free() : void
 {
 $this->statement->closeCursor();
 }
 private function fetch(int $mode)
 {
 try {
 return $this->statement->fetch($mode);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 private function fetchAll(int $mode) : array
 {
 try {
 return $this->statement->fetchAll($mode);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
}
