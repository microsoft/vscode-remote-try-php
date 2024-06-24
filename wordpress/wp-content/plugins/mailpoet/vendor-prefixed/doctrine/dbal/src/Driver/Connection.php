<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
interface Connection
{
 public function prepare(string $sql) : Statement;
 public function query(string $sql) : Result;
 public function quote($value, $type = ParameterType::STRING);
 public function exec(string $sql) : int;
 public function lastInsertId($name = null);
 public function beginTransaction();
 public function commit();
 public function rollBack();
}
