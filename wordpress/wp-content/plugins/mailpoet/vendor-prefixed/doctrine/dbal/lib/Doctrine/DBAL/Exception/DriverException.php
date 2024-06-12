<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\DriverException as DeprecatedDriverException;
use MailPoetVendor\Doctrine\DBAL\Exception;
class DriverException extends Exception
{
 private $driverException;
 public function __construct($message, DeprecatedDriverException $driverException)
 {
 parent::__construct($message, 0, $driverException);
 $this->driverException = $driverException;
 }
 public function getErrorCode()
 {
 return $this->driverException->getErrorCode();
 }
 public function getSQLState()
 {
 return $this->driverException->getSQLState();
 }
}
