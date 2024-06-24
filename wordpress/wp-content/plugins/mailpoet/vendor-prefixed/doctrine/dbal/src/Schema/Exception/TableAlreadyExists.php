<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
final class TableAlreadyExists extends SchemaException
{
 public static function new(string $tableName) : self
 {
 return new self(sprintf('The table with name "%s" already exists.', $tableName), self::TABLE_ALREADY_EXISTS);
 }
}
