<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception as DriverException;
final class PDOException extends \PDOException implements DriverException
{
 private ?string $sqlState = null;
 public static function new(\PDOException $previous) : self
 {
 $exception = new self($previous->message, 0, $previous);
 $exception->errorInfo = $previous->errorInfo;
 $exception->code = $previous->code;
 $exception->sqlState = $previous->errorInfo[0] ?? null;
 return $exception;
 }
 public function getSQLState() : ?string
 {
 return $this->sqlState;
 }
}
