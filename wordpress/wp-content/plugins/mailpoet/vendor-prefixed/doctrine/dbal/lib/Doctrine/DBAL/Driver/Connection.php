<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
interface Connection
{
 public function prepare($sql);
 public function query();
 public function quote($value, $type = ParameterType::STRING);
 public function exec($sql);
 public function lastInsertId($name = null);
 public function beginTransaction();
 public function commit();
 public function rollBack();
 public function errorCode();
 public function errorInfo();
}
