<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\PgSQL\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use UnexpectedValueException;
use function sprintf;
final class UnexpectedValue extends UnexpectedValueException implements Exception
{
 public static function new(string $value, string $type) : self
 {
 return new self(sprintf('Unexpected value "%s" of type "%s" returned by Postgres', $value, $type));
 }
 public function getSQLState()
 {
 return null;
 }
}
