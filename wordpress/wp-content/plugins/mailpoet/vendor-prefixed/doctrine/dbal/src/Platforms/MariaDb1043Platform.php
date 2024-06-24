<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\JsonType;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function sprintf;
class MariaDb1043Platform extends MariaDb1027Platform
{
 public function getJsonTypeDeclarationSQL(array $column) : string
 {
 return 'JSON';
 }
 public function getListTableColumnsSQL($table, $database = null) : string
 {
 // @todo 4.0 - call getColumnTypeSQLSnippet() instead
 [$columnTypeSQL, $joinCheckConstraintSQL] = $this->getColumnTypeSQLSnippets('c', $database);
 return sprintf(<<<SQL
SELECT c.COLUMN_NAME AS Field,
 {$columnTypeSQL} AS Type,
 c.IS_NULLABLE AS `Null`,
 c.COLUMN_KEY AS `Key`,
 c.COLUMN_DEFAULT AS `Default`,
 c.EXTRA AS Extra,
 c.COLUMN_COMMENT AS Comment,
 c.CHARACTER_SET_NAME AS CharacterSet,
 c.COLLATION_NAME AS Collation
FROM information_schema.COLUMNS c
 {$joinCheckConstraintSQL}
WHERE c.TABLE_SCHEMA = %s
AND c.TABLE_NAME = %s
ORDER BY ORDINAL_POSITION ASC;
SQL
, $this->getDatabaseNameSQL($database), $this->quoteStringLiteral($table));
 }
 public function getColumnTypeSQLSnippet(string $tableAlias = 'c', ?string $databaseName = null) : string
 {
 if ($this->getJsonTypeDeclarationSQL([]) !== 'JSON') {
 return parent::getColumnTypeSQLSnippet($tableAlias, $databaseName);
 }
 if ($databaseName === null) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6215', 'Not passing a database name to methods "getColumnTypeSQLSnippet()", ' . '"getColumnTypeSQLSnippets()", and "getListTableColumnsSQL()" of "%s" is deprecated.', self::class);
 }
 $subQueryAlias = 'i_' . $tableAlias;
 $databaseName = $this->getDatabaseNameSQL($databaseName);
 // The check for `CONSTRAINT_SCHEMA = $databaseName` is mandatory here to prevent performance issues
 return <<<SQL
 IF(
 {$tableAlias}.COLUMN_TYPE = 'longtext'
 AND EXISTS(
 SELECT * from information_schema.CHECK_CONSTRAINTS {$subQueryAlias}
 WHERE {$subQueryAlias}.CONSTRAINT_SCHEMA = {$databaseName}
 AND {$subQueryAlias}.TABLE_NAME = {$tableAlias}.TABLE_NAME
 AND {$subQueryAlias}.CHECK_CLAUSE = CONCAT(
 'json_valid(`',
 {$tableAlias}.COLUMN_NAME,
 '`)'
 )
 ),
 'json',
 {$tableAlias}.COLUMN_TYPE
 )
SQL;
 }
 public function getColumnDeclarationSQL($name, array $column)
 {
 // MariaDb forces column collation to utf8mb4_bin where the column was declared as JSON so ignore
 // collation and character set for json columns as attempting to set them can cause an error.
 if ($this->getJsonTypeDeclarationSQL([]) === 'JSON' && ($column['type'] ?? null) instanceof JsonType) {
 unset($column['collation']);
 unset($column['charset']);
 }
 return parent::getColumnDeclarationSQL($name, $column);
 }
}
