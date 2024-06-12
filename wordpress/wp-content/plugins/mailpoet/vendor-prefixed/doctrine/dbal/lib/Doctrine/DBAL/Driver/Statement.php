<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
interface Statement extends ResultStatement
{
 public function bindValue($param, $value, $type = ParameterType::STRING);
 public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null);
 public function errorCode();
 public function errorInfo();
 public function execute($params = null);
 public function rowCount();
}
