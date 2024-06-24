<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\ColumnAlreadyExists;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\ColumnDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\ForeignKeyDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\IndexAlreadyExists;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\IndexDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\IndexNameInvalid;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\NamedForeignKeyRequired;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\NamespaceAlreadyExists;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\SequenceAlreadyExists;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\SequenceDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\TableAlreadyExists;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\TableDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Schema\Exception\UniqueConstraintDoesNotExist;
use function sprintf;
class SchemaException extends Exception
{
 public const TABLE_DOESNT_EXIST = 10;
 public const TABLE_ALREADY_EXISTS = 20;
 public const COLUMN_DOESNT_EXIST = 30;
 public const COLUMN_ALREADY_EXISTS = 40;
 public const INDEX_DOESNT_EXIST = 50;
 public const INDEX_ALREADY_EXISTS = 60;
 public const SEQUENCE_DOENST_EXIST = 70;
 public const SEQUENCE_ALREADY_EXISTS = 80;
 public const INDEX_INVALID_NAME = 90;
 public const FOREIGNKEY_DOESNT_EXIST = 100;
 public const CONSTRAINT_DOESNT_EXIST = 110;
 public const NAMESPACE_ALREADY_EXISTS = 120;
 public static function tableDoesNotExist($tableName)
 {
 return TableDoesNotExist::new($tableName);
 }
 public static function indexNameInvalid($indexName)
 {
 return IndexNameInvalid::new($indexName);
 }
 public static function indexDoesNotExist($indexName, $table)
 {
 return IndexDoesNotExist::new($indexName, $table);
 }
 public static function indexAlreadyExists($indexName, $table)
 {
 return IndexAlreadyExists::new($indexName, $table);
 }
 public static function columnDoesNotExist($columnName, $table)
 {
 return ColumnDoesNotExist::new($columnName, $table);
 }
 public static function namespaceAlreadyExists($namespaceName)
 {
 return NamespaceAlreadyExists::new($namespaceName);
 }
 public static function tableAlreadyExists($tableName)
 {
 return TableAlreadyExists::new($tableName);
 }
 public static function columnAlreadyExists($tableName, $columnName)
 {
 return ColumnAlreadyExists::new($tableName, $columnName);
 }
 public static function sequenceAlreadyExists($name)
 {
 return SequenceAlreadyExists::new($name);
 }
 public static function sequenceDoesNotExist($name)
 {
 return SequenceDoesNotExist::new($name);
 }
 public static function uniqueConstraintDoesNotExist($constraintName, $table)
 {
 return UniqueConstraintDoesNotExist::new($constraintName, $table);
 }
 public static function foreignKeyDoesNotExist($fkName, $table)
 {
 return ForeignKeyDoesNotExist::new($fkName, $table);
 }
 public static function namedForeignKeyRequired(Table $localTable, ForeignKeyConstraint $foreignKey)
 {
 return NamedForeignKeyRequired::new($localTable, $foreignKey);
 }
 public static function alterTableChangeNotSupported($changeName)
 {
 return new self(sprintf("Alter table change not supported, given '%s'", $changeName));
 }
}
