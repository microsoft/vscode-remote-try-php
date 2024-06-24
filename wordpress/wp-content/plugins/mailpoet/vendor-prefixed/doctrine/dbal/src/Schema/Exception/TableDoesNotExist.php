<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
final class TableDoesNotExist extends SchemaException
{
 public static function new(string $tableName) : self
 {
 return new self(sprintf('There is no table with name "%s" in the schema.', $tableName), self::TABLE_DOESNT_EXIST);
 }
}
