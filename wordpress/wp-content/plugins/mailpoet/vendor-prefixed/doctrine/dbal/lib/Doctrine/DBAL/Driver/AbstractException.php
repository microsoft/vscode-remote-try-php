<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use Exception as BaseException;
use Throwable;
abstract class AbstractException extends BaseException implements DriverException
{
 private $errorCode;
 private $sqlState;
 public function __construct($message, $sqlState = null, $errorCode = null, ?Throwable $previous = null)
 {
 parent::__construct($message, 0, $previous);
 $this->errorCode = $errorCode;
 $this->sqlState = $sqlState;
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
