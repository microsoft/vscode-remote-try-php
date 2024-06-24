<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
final class ColumnDoesNotExist extends SchemaException
{
 public static function new(string $columnName, string $table) : self
 {
 return new self(sprintf('There is no column with name "%s" on table "%s".', $columnName, $table), self::COLUMN_DOESNT_EXIST);
 }
}
