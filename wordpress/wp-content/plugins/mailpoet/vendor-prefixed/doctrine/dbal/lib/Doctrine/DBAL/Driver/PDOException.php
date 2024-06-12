<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Exception;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class PDOException extends \PDOException implements DriverException
{
 private $errorCode;
 private $sqlState;
 public function __construct(\PDOException $exception)
 {
 parent::__construct($exception->getMessage(), 0, $exception);
 $this->code = $exception->getCode();
 $this->errorInfo = $exception->errorInfo;
 $this->errorCode = $exception->errorInfo[1] ?? $exception->getCode();
 $this->sqlState = $exception->errorInfo[0] ?? $exception->getCode();
 }
 public function getErrorCode()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4112', 'Driver\\AbstractException::getErrorCode() is deprecated, use getSQLState() or getCode() instead.');
 return $this->errorCode;
 }
 public function getSQLState()
 {
 return $this->sqlState;
 }
}
