<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\PDOException;
final class Exception extends PDOException
{
 public static function new(\PDOException $exception) : self
 {
 return new self($exception);
 }
}
