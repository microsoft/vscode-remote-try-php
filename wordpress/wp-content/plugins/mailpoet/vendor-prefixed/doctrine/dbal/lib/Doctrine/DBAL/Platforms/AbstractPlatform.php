<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaAlterTableAddColumnEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaAlterTableEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaAlterTableRemoveColumnEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaAlterTableRenameColumnEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaCreateTableColumnEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaCreateTableEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaDropTableEventArgs;
use MailPoetVendor\Doctrine\DBAL\Events;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\Keywords\KeywordList;
use MailPoetVendor\Doctrine\DBAL\Schema\Column;
use MailPoetVendor\Doctrine\DBAL\Schema\ColumnDiff;
use MailPoetVendor\Doctrine\DBAL\Schema\Constraint;
use MailPoetVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use MailPoetVendor\Doctrine\DBAL\Schema\Identifier;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\Sequence;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
use MailPoetVendor\Doctrine\DBAL\TransactionIsolationLevel;
use MailPoetVendor\Doctrine\DBAL\Types;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use InvalidArgumentException;
use UnexpectedValueException;
use function addcslashes;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function assert;
use function count;
use function explode;
use function func_get_arg;
use function func_get_args;
use function func_num_args;
use function implode;
use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function strtolower;
use function strtoupper;
abstract class AbstractPlatform
{
 public const CREATE_INDEXES = 1;
 public const CREATE_FOREIGNKEYS = 2;
 public const DATE_INTERVAL_UNIT_SECOND = DateIntervalUnit::SECOND;
 public const DATE_INTERVAL_UNIT_MINUTE = DateIntervalUnit::MINUTE;
 public const DATE_INTERVAL_UNIT_HOUR = DateIntervalUnit::HOUR;
 public const DATE_INTERVAL_UNIT_DAY = DateIntervalUnit::DAY;
 public const DATE_INTERVAL_UNIT_WEEK = DateIntervalUnit::WEEK;
 public const DATE_INTERVAL_UNIT_MONTH = DateIntervalUnit::MONTH;
 public const DATE_INTERVAL_UNIT_QUARTER = DateIntervalUnit::QUARTER;
 public const DATE_INTERVAL_UNIT_YEAR = DateIntervalUnit::YEAR;
 public const TRIM_UNSPECIFIED = TrimMode::UNSPECIFIED;
 public const TRIM_LEADING = TrimMode::LEADING;
 public const TRIM_TRAILING = TrimMode::TRAILING;
 public const TRIM_BOTH = TrimMode::BOTH;
 protected $doctrineTypeMapping;
 protected $doctrineTypeComments;
 protected $_eventManager;
 protected $_keywords;
 public function __construct()
 {
 }
 public function setEventManager(EventManager $eventManager)
 {
 $this->_eventManager = $eventManager;
 }
 public function getEventManager()
 {
 return $this->_eventManager;
 }
 public abstract function getBooleanTypeDeclarationSQL(array $column);
 public abstract function getIntegerTypeDeclarationSQL(array $column);
 public abstract function getBigIntTypeDeclarationSQL(array $column);
 public abstract function getSmallIntTypeDeclarationSQL(array $column);
 protected abstract function _getCommonIntegerTypeDeclarationSQL(array $column);
 protected abstract function initializeDoctrineTypeMappings();
 private function initializeAllDoctrineTypeMappings()
 {
 $this->initializeDoctrineTypeMappings();
 foreach (Type::getTypesMap() as $typeName => $className) {
 foreach (Type::getType($typeName)->getMappedDatabaseTypes($this) as $dbType) {
 $this->doctrineTypeMapping[$dbType] = $typeName;
 }
 }
 }
 public function getAsciiStringTypeDeclarationSQL(array $column) : string
 {
 return $this->getVarcharTypeDeclarationSQL($column);
 }
 public function getVarcharTypeDeclarationSQL(array $column)
 {
 if (!isset($column['length'])) {
 $column['length'] = $this->getVarcharDefaultLength();
 }
 $fixed = $column['fixed'] ?? \false;
 $maxLength = $fixed ? $this->getCharMaxLength() : $this->getVarcharMaxLength();
 if ($column['length'] > $maxLength) {
 return $this->getClobTypeDeclarationSQL($column);
 }
 return $this->getVarcharTypeDeclarationSQLSnippet($column['length'], $fixed);
 }
 public function getBinaryTypeDeclarationSQL(array $column)
 {
 if (!isset($column['length'])) {
 $column['length'] = $this->getBinaryDefaultLength();
 }
 $fixed = $column['fixed'] ?? \false;
 $maxLength = $this->getBinaryMaxLength();
 if ($column['length'] > $maxLength) {
 if ($maxLength > 0) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3187', 'Binary column length %d is greater than supported by the platform (%d).' . ' Reduce the column length or use a BLOB column instead.', $column['length'], $maxLength);
 }
 return $this->getBlobTypeDeclarationSQL($column);
 }
 return $this->getBinaryTypeDeclarationSQLSnippet($column['length'], $fixed);
 }
 public function getGuidTypeDeclarationSQL(array $column)
 {
 $column['length'] = 36;
 $column['fixed'] = \true;
 return $this->getVarcharTypeDeclarationSQL($column);
 }
 public function getJsonTypeDeclarationSQL(array $column)
 {
 return $this->getClobTypeDeclarationSQL($column);
 }
 protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
 {
 throw Exception::notSupported('VARCHARs not supported by Platform.');
 }
 protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed)
 {
 throw Exception::notSupported('BINARY/VARBINARY column types are not supported by this platform.');
 }
 public abstract function getClobTypeDeclarationSQL(array $column);
 public abstract function getBlobTypeDeclarationSQL(array $column);
 public abstract function getName();
 public function registerDoctrineTypeMapping($dbType, $doctrineType)
 {
 if ($this->doctrineTypeMapping === null) {
 $this->initializeAllDoctrineTypeMappings();
 }
 if (!Types\Type::hasType($doctrineType)) {
 throw Exception::typeNotFound($doctrineType);
 }
 $dbType = strtolower($dbType);
 $this->doctrineTypeMapping[$dbType] = $doctrineType;
 $doctrineType = Type::getType($doctrineType);
 if (!$doctrineType->requiresSQLCommentHint($this)) {
 return;
 }
 $this->markDoctrineTypeCommented($doctrineType);
 }
 public function getDoctrineTypeMapping($dbType)
 {
 if ($this->doctrineTypeMapping === null) {
 $this->initializeAllDoctrineTypeMappings();
 }
 $dbType = strtolower($dbType);
 if (!isset($this->doctrineTypeMapping[$dbType])) {
 throw new Exception('Unknown database type ' . $dbType . ' requested, ' . static::class . ' may not support it.');
 }
 return $this->doctrineTypeMapping[$dbType];
 }
 public function hasDoctrineTypeMappingFor($dbType)
 {
 if ($this->doctrineTypeMapping === null) {
 $this->initializeAllDoctrineTypeMappings();
 }
 $dbType = strtolower($dbType);
 return isset($this->doctrineTypeMapping[$dbType]);
 }
 protected function initializeCommentedDoctrineTypes()
 {
 $this->doctrineTypeComments = [];
 foreach (Type::getTypesMap() as $typeName => $className) {
 $type = Type::getType($typeName);
 if (!$type->requiresSQLCommentHint($this)) {
 continue;
 }
 $this->doctrineTypeComments[] = $typeName;
 }
 }
 public function isCommentedDoctrineType(Type $doctrineType)
 {
 if ($this->doctrineTypeComments === null) {
 $this->initializeCommentedDoctrineTypes();
 }
 assert(is_array($this->doctrineTypeComments));
 return in_array($doctrineType->getName(), $this->doctrineTypeComments);
 }
 public function markDoctrineTypeCommented($doctrineType)
 {
 if ($this->doctrineTypeComments === null) {
 $this->initializeCommentedDoctrineTypes();
 }
 assert(is_array($this->doctrineTypeComments));
 $this->doctrineTypeComments[] = $doctrineType instanceof Type ? $doctrineType->getName() : $doctrineType;
 }
 public function getDoctrineTypeComment(Type $doctrineType)
 {
 return '(DC2Type:' . $doctrineType->getName() . ')';
 }
 protected function getColumnComment(Column $column)
 {
 $comment = $column->getComment();
 if ($this->isCommentedDoctrineType($column->getType())) {
 $comment .= $this->getDoctrineTypeComment($column->getType());
 }
 return $comment;
 }
 public function getIdentifierQuoteCharacter()
 {
 return '"';
 }
 public function getSqlCommentStartString()
 {
 return '--';
 }
 public function getSqlCommentEndString()
 {
 return "\n";
 }
 public function getCharMaxLength() : int
 {
 return $this->getVarcharMaxLength();
 }
 public function getVarcharMaxLength()
 {
 return 4000;
 }
 public function getVarcharDefaultLength()
 {
 return 255;
 }
 public function getBinaryMaxLength()
 {
 return 4000;
 }
 public function getBinaryDefaultLength()
 {
 return 255;
 }
 public function getWildcards()
 {
 return ['%', '_'];
 }
 public function getRegexpExpression()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getGuidExpression()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getAvgExpression($column)
 {
 return 'AVG(' . $column . ')';
 }
 public function getCountExpression($column)
 {
 return 'COUNT(' . $column . ')';
 }
 public function getMaxExpression($column)
 {
 return 'MAX(' . $column . ')';
 }
 public function getMinExpression($column)
 {
 return 'MIN(' . $column . ')';
 }
 public function getSumExpression($column)
 {
 return 'SUM(' . $column . ')';
 }
 // scalar functions
 public function getMd5Expression($column)
 {
 return 'MD5(' . $column . ')';
 }
 public function getLengthExpression($column)
 {
 return 'LENGTH(' . $column . ')';
 }
 public function getSqrtExpression($column)
 {
 return 'SQRT(' . $column . ')';
 }
 public function getRoundExpression($column, $decimals = 0)
 {
 return 'ROUND(' . $column . ', ' . $decimals . ')';
 }
 public function getModExpression($expression1, $expression2)
 {
 return 'MOD(' . $expression1 . ', ' . $expression2 . ')';
 }
 public function getTrimExpression($str, $mode = TrimMode::UNSPECIFIED, $char = \false)
 {
 $expression = '';
 switch ($mode) {
 case TrimMode::LEADING:
 $expression = 'LEADING ';
 break;
 case TrimMode::TRAILING:
 $expression = 'TRAILING ';
 break;
 case TrimMode::BOTH:
 $expression = 'BOTH ';
 break;
 }
 if ($char !== \false) {
 $expression .= $char . ' ';
 }
 if ($mode || $char !== \false) {
 $expression .= 'FROM ';
 }
 return 'TRIM(' . $expression . $str . ')';
 }
 public function getRtrimExpression($str)
 {
 return 'RTRIM(' . $str . ')';
 }
 public function getLtrimExpression($str)
 {
 return 'LTRIM(' . $str . ')';
 }
 public function getUpperExpression($str)
 {
 return 'UPPER(' . $str . ')';
 }
 public function getLowerExpression($str)
 {
 return 'LOWER(' . $str . ')';
 }
 public function getLocateExpression($str, $substr, $startPos = \false)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getNowExpression()
 {
 return 'NOW()';
 }
 public function getSubstringExpression($string, $start, $length = null)
 {
 if ($length === null) {
 return 'SUBSTRING(' . $string . ' FROM ' . $start . ')';
 }
 return 'SUBSTRING(' . $string . ' FROM ' . $start . ' FOR ' . $length . ')';
 }
 public function getConcatExpression()
 {
 return implode(' || ', func_get_args());
 }
 public function getNotExpression($expression)
 {
 return 'NOT(' . $expression . ')';
 }
 public function getIsNullExpression($expression)
 {
 return $expression . ' IS NULL';
 }
 public function getIsNotNullExpression($expression)
 {
 return $expression . ' IS NOT NULL';
 }
 public function getBetweenExpression($expression, $value1, $value2)
 {
 return $expression . ' BETWEEN ' . $value1 . ' AND ' . $value2;
 }
 public function getAcosExpression($value)
 {
 return 'ACOS(' . $value . ')';
 }
 public function getSinExpression($value)
 {
 return 'SIN(' . $value . ')';
 }
 public function getPiExpression()
 {
 return 'PI()';
 }
 public function getCosExpression($value)
 {
 return 'COS(' . $value . ')';
 }
 public function getDateDiffExpression($date1, $date2)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDateAddSecondsExpression($date, $seconds)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $seconds, DateIntervalUnit::SECOND);
 }
 public function getDateSubSecondsExpression($date, $seconds)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $seconds, DateIntervalUnit::SECOND);
 }
 public function getDateAddMinutesExpression($date, $minutes)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $minutes, DateIntervalUnit::MINUTE);
 }
 public function getDateSubMinutesExpression($date, $minutes)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $minutes, DateIntervalUnit::MINUTE);
 }
 public function getDateAddHourExpression($date, $hours)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $hours, DateIntervalUnit::HOUR);
 }
 public function getDateSubHourExpression($date, $hours)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $hours, DateIntervalUnit::HOUR);
 }
 public function getDateAddDaysExpression($date, $days)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $days, DateIntervalUnit::DAY);
 }
 public function getDateSubDaysExpression($date, $days)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $days, DateIntervalUnit::DAY);
 }
 public function getDateAddWeeksExpression($date, $weeks)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $weeks, DateIntervalUnit::WEEK);
 }
 public function getDateSubWeeksExpression($date, $weeks)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $weeks, DateIntervalUnit::WEEK);
 }
 public function getDateAddMonthExpression($date, $months)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $months, DateIntervalUnit::MONTH);
 }
 public function getDateSubMonthExpression($date, $months)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $months, DateIntervalUnit::MONTH);
 }
 public function getDateAddQuartersExpression($date, $quarters)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $quarters, DateIntervalUnit::QUARTER);
 }
 public function getDateSubQuartersExpression($date, $quarters)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $quarters, DateIntervalUnit::QUARTER);
 }
 public function getDateAddYearsExpression($date, $years)
 {
 return $this->getDateArithmeticIntervalExpression($date, '+', $years, DateIntervalUnit::YEAR);
 }
 public function getDateSubYearsExpression($date, $years)
 {
 return $this->getDateArithmeticIntervalExpression($date, '-', $years, DateIntervalUnit::YEAR);
 }
 protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getBitAndComparisonExpression($value1, $value2)
 {
 return '(' . $value1 . ' & ' . $value2 . ')';
 }
 public function getBitOrComparisonExpression($value1, $value2)
 {
 return '(' . $value1 . ' | ' . $value2 . ')';
 }
 public function getForUpdateSQL()
 {
 return 'FOR UPDATE';
 }
 public function appendLockHint($fromClause, $lockMode)
 {
 return $fromClause;
 }
 public function getReadLockSQL()
 {
 return $this->getForUpdateSQL();
 }
 public function getWriteLockSQL()
 {
 return $this->getForUpdateSQL();
 }
 public function getDropDatabaseSQL($name)
 {
 return 'DROP DATABASE ' . $name;
 }
 public function getDropTableSQL($table)
 {
 $tableArg = $table;
 if ($table instanceof Table) {
 $table = $table->getQuotedName($this);
 }
 if (!is_string($table)) {
 throw new InvalidArgumentException(__METHOD__ . '() expects $table parameter to be string or ' . Table::class . '.');
 }
 if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaDropTable)) {
 $eventArgs = new SchemaDropTableEventArgs($tableArg, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaDropTable, $eventArgs);
 if ($eventArgs->isDefaultPrevented()) {
 $sql = $eventArgs->getSql();
 if ($sql === null) {
 throw new UnexpectedValueException('Default implementation of DROP TABLE was overridden with NULL');
 }
 return $sql;
 }
 }
 return 'DROP TABLE ' . $table;
 }
 public function getDropTemporaryTableSQL($table)
 {
 return $this->getDropTableSQL($table);
 }
 public function getDropIndexSQL($index, $table = null)
 {
 if ($index instanceof Index) {
 $index = $index->getQuotedName($this);
 } elseif (!is_string($index)) {
 throw new InvalidArgumentException(__METHOD__ . '() expects $index parameter to be string or ' . Index::class . '.');
 }
 return 'DROP INDEX ' . $index;
 }
 public function getDropConstraintSQL($constraint, $table)
 {
 if (!$constraint instanceof Constraint) {
 $constraint = new Identifier($constraint);
 }
 if (!$table instanceof Table) {
 $table = new Identifier($table);
 }
 $constraint = $constraint->getQuotedName($this);
 $table = $table->getQuotedName($this);
 return 'ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $constraint;
 }
 public function getDropForeignKeySQL($foreignKey, $table)
 {
 if (!$foreignKey instanceof ForeignKeyConstraint) {
 $foreignKey = new Identifier($foreignKey);
 }
 if (!$table instanceof Table) {
 $table = new Identifier($table);
 }
 $foreignKey = $foreignKey->getQuotedName($this);
 $table = $table->getQuotedName($this);
 return 'ALTER TABLE ' . $table . ' DROP FOREIGN KEY ' . $foreignKey;
 }
 public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES)
 {
 if (!is_int($createFlags)) {
 throw new InvalidArgumentException('Second argument of AbstractPlatform::getCreateTableSQL() has to be integer.');
 }
 if (count($table->getColumns()) === 0) {
 throw Exception::noColumnsSpecifiedForTable($table->getName());
 }
 $tableName = $table->getQuotedName($this);
 $options = $table->getOptions();
 $options['uniqueConstraints'] = [];
 $options['indexes'] = [];
 $options['primary'] = [];
 if (($createFlags & self::CREATE_INDEXES) > 0) {
 foreach ($table->getIndexes() as $index) {
 if ($index->isPrimary()) {
 $options['primary'] = $index->getQuotedColumns($this);
 $options['primary_index'] = $index;
 } else {
 $options['indexes'][$index->getQuotedName($this)] = $index;
 }
 }
 }
 $columnSql = [];
 $columns = [];
 foreach ($table->getColumns() as $column) {
 if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaCreateTableColumn)) {
 $eventArgs = new SchemaCreateTableColumnEventArgs($column, $table, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaCreateTableColumn, $eventArgs);
 $columnSql = array_merge($columnSql, $eventArgs->getSql());
 if ($eventArgs->isDefaultPrevented()) {
 continue;
 }
 }
 $name = $column->getQuotedName($this);
 $columnData = array_merge($column->toArray(), ['name' => $name, 'version' => $column->hasPlatformOption('version') ? $column->getPlatformOption('version') : \false, 'comment' => $this->getColumnComment($column)]);
 if ($columnData['type'] instanceof Types\StringType && $columnData['length'] === null) {
 $columnData['length'] = 255;
 }
 if (in_array($column->getName(), $options['primary'])) {
 $columnData['primary'] = \true;
 }
 $columns[$name] = $columnData;
 }
 if (($createFlags & self::CREATE_FOREIGNKEYS) > 0) {
 $options['foreignKeys'] = [];
 foreach ($table->getForeignKeys() as $fkConstraint) {
 $options['foreignKeys'][] = $fkConstraint;
 }
 }
 if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaCreateTable)) {
 $eventArgs = new SchemaCreateTableEventArgs($table, $columns, $options, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaCreateTable, $eventArgs);
 if ($eventArgs->isDefaultPrevented()) {
 return array_merge($eventArgs->getSql(), $columnSql);
 }
 }
 $sql = $this->_getCreateTableSQL($tableName, $columns, $options);
 if ($this->supportsCommentOnStatement()) {
 if ($table->hasOption('comment')) {
 $sql[] = $this->getCommentOnTableSQL($tableName, $table->getOption('comment'));
 }
 foreach ($table->getColumns() as $column) {
 $comment = $this->getColumnComment($column);
 if ($comment === null || $comment === '') {
 continue;
 }
 $sql[] = $this->getCommentOnColumnSQL($tableName, $column->getQuotedName($this), $comment);
 }
 }
 return array_merge($sql, $columnSql);
 }
 protected function getCommentOnTableSQL(string $tableName, ?string $comment) : string
 {
 $tableName = new Identifier($tableName);
 return sprintf('COMMENT ON TABLE %s IS %s', $tableName->getQuotedName($this), $this->quoteStringLiteral((string) $comment));
 }
 public function getCommentOnColumnSQL($tableName, $columnName, $comment)
 {
 $tableName = new Identifier($tableName);
 $columnName = new Identifier($columnName);
 return sprintf('COMMENT ON COLUMN %s.%s IS %s', $tableName->getQuotedName($this), $columnName->getQuotedName($this), $this->quoteStringLiteral((string) $comment));
 }
 public function getInlineColumnCommentSQL($comment)
 {
 if (!$this->supportsInlineColumnComments()) {
 throw Exception::notSupported(__METHOD__);
 }
 return 'COMMENT ' . $this->quoteStringLiteral($comment);
 }
 protected function _getCreateTableSQL($name, array $columns, array $options = [])
 {
 $columnListSql = $this->getColumnDeclarationListSQL($columns);
 if (isset($options['uniqueConstraints']) && !empty($options['uniqueConstraints'])) {
 foreach ($options['uniqueConstraints'] as $index => $definition) {
 $columnListSql .= ', ' . $this->getUniqueConstraintDeclarationSQL($index, $definition);
 }
 }
 if (isset($options['primary']) && !empty($options['primary'])) {
 $columnListSql .= ', PRIMARY KEY(' . implode(', ', array_unique(array_values($options['primary']))) . ')';
 }
 if (isset($options['indexes']) && !empty($options['indexes'])) {
 foreach ($options['indexes'] as $index => $definition) {
 $columnListSql .= ', ' . $this->getIndexDeclarationSQL($index, $definition);
 }
 }
 $query = 'CREATE TABLE ' . $name . ' (' . $columnListSql;
 $check = $this->getCheckDeclarationSQL($columns);
 if (!empty($check)) {
 $query .= ', ' . $check;
 }
 $query .= ')';
 $sql = [$query];
 if (isset($options['foreignKeys'])) {
 foreach ((array) $options['foreignKeys'] as $definition) {
 $sql[] = $this->getCreateForeignKeySQL($definition, $name);
 }
 }
 return $sql;
 }
 public function getCreateTemporaryTableSnippetSQL()
 {
 return 'CREATE TEMPORARY TABLE';
 }
 public function getCreateSequenceSQL(Sequence $sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getAlterSequenceSQL(Sequence $sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getCreateConstraintSQL(Constraint $constraint, $table)
 {
 if ($table instanceof Table) {
 $table = $table->getQuotedName($this);
 }
 $query = 'ALTER TABLE ' . $table . ' ADD CONSTRAINT ' . $constraint->getQuotedName($this);
 $columnList = '(' . implode(', ', $constraint->getQuotedColumns($this)) . ')';
 $referencesClause = '';
 if ($constraint instanceof Index) {
 if ($constraint->isPrimary()) {
 $query .= ' PRIMARY KEY';
 } elseif ($constraint->isUnique()) {
 $query .= ' UNIQUE';
 } else {
 throw new InvalidArgumentException('Can only create primary or unique constraints, no common indexes with getCreateConstraintSQL().');
 }
 } elseif ($constraint instanceof ForeignKeyConstraint) {
 $query .= ' FOREIGN KEY';
 $referencesClause = ' REFERENCES ' . $constraint->getQuotedForeignTableName($this) . ' (' . implode(', ', $constraint->getQuotedForeignColumns($this)) . ')';
 }
 $query .= ' ' . $columnList . $referencesClause;
 return $query;
 }
 public function getCreateIndexSQL(Index $index, $table)
 {
 if ($table instanceof Table) {
 $table = $table->getQuotedName($this);
 }
 $name = $index->getQuotedName($this);
 $columns = $index->getColumns();
 if (count($columns) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'columns' required.");
 }
 if ($index->isPrimary()) {
 return $this->getCreatePrimaryKeySQL($index, $table);
 }
 $query = 'CREATE ' . $this->getCreateIndexSQLFlags($index) . 'INDEX ' . $name . ' ON ' . $table;
 $query .= ' (' . $this->getIndexFieldDeclarationListSQL($index) . ')' . $this->getPartialIndexSQL($index);
 return $query;
 }
 protected function getPartialIndexSQL(Index $index)
 {
 if ($this->supportsPartialIndexes() && $index->hasOption('where')) {
 return ' WHERE ' . $index->getOption('where');
 }
 return '';
 }
 protected function getCreateIndexSQLFlags(Index $index)
 {
 return $index->isUnique() ? 'UNIQUE ' : '';
 }
 public function getCreatePrimaryKeySQL(Index $index, $table)
 {
 if ($table instanceof Table) {
 $table = $table->getQuotedName($this);
 }
 return 'ALTER TABLE ' . $table . ' ADD PRIMARY KEY (' . $this->getIndexFieldDeclarationListSQL($index) . ')';
 }
 public function getCreateSchemaSQL($schemaName)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function quoteIdentifier($str)
 {
 if (strpos($str, '.') !== \false) {
 $parts = array_map([$this, 'quoteSingleIdentifier'], explode('.', $str));
 return implode('.', $parts);
 }
 return $this->quoteSingleIdentifier($str);
 }
 public function quoteSingleIdentifier($str)
 {
 $c = $this->getIdentifierQuoteCharacter();
 return $c . str_replace($c, $c . $c, $str) . $c;
 }
 public function getCreateForeignKeySQL(ForeignKeyConstraint $foreignKey, $table)
 {
 if ($table instanceof Table) {
 $table = $table->getQuotedName($this);
 }
 return 'ALTER TABLE ' . $table . ' ADD ' . $this->getForeignKeyDeclarationSQL($foreignKey);
 }
 public function getAlterTableSQL(TableDiff $diff)
 {
 throw Exception::notSupported(__METHOD__);
 }
 protected function onSchemaAlterTableAddColumn(Column $column, TableDiff $diff, &$columnSql)
 {
 if ($this->_eventManager === null) {
 return \false;
 }
 if (!$this->_eventManager->hasListeners(Events::onSchemaAlterTableAddColumn)) {
 return \false;
 }
 $eventArgs = new SchemaAlterTableAddColumnEventArgs($column, $diff, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaAlterTableAddColumn, $eventArgs);
 $columnSql = array_merge($columnSql, $eventArgs->getSql());
 return $eventArgs->isDefaultPrevented();
 }
 protected function onSchemaAlterTableRemoveColumn(Column $column, TableDiff $diff, &$columnSql)
 {
 if ($this->_eventManager === null) {
 return \false;
 }
 if (!$this->_eventManager->hasListeners(Events::onSchemaAlterTableRemoveColumn)) {
 return \false;
 }
 $eventArgs = new SchemaAlterTableRemoveColumnEventArgs($column, $diff, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaAlterTableRemoveColumn, $eventArgs);
 $columnSql = array_merge($columnSql, $eventArgs->getSql());
 return $eventArgs->isDefaultPrevented();
 }
 protected function onSchemaAlterTableChangeColumn(ColumnDiff $columnDiff, TableDiff $diff, &$columnSql)
 {
 if ($this->_eventManager === null) {
 return \false;
 }
 if (!$this->_eventManager->hasListeners(Events::onSchemaAlterTableChangeColumn)) {
 return \false;
 }
 $eventArgs = new SchemaAlterTableChangeColumnEventArgs($columnDiff, $diff, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaAlterTableChangeColumn, $eventArgs);
 $columnSql = array_merge($columnSql, $eventArgs->getSql());
 return $eventArgs->isDefaultPrevented();
 }
 protected function onSchemaAlterTableRenameColumn($oldColumnName, Column $column, TableDiff $diff, &$columnSql)
 {
 if ($this->_eventManager === null) {
 return \false;
 }
 if (!$this->_eventManager->hasListeners(Events::onSchemaAlterTableRenameColumn)) {
 return \false;
 }
 $eventArgs = new SchemaAlterTableRenameColumnEventArgs($oldColumnName, $column, $diff, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaAlterTableRenameColumn, $eventArgs);
 $columnSql = array_merge($columnSql, $eventArgs->getSql());
 return $eventArgs->isDefaultPrevented();
 }
 protected function onSchemaAlterTable(TableDiff $diff, &$sql)
 {
 if ($this->_eventManager === null) {
 return \false;
 }
 if (!$this->_eventManager->hasListeners(Events::onSchemaAlterTable)) {
 return \false;
 }
 $eventArgs = new SchemaAlterTableEventArgs($diff, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaAlterTable, $eventArgs);
 $sql = array_merge($sql, $eventArgs->getSql());
 return $eventArgs->isDefaultPrevented();
 }
 protected function getPreAlterTableIndexForeignKeySQL(TableDiff $diff)
 {
 $tableName = $diff->getName($this)->getQuotedName($this);
 $sql = [];
 if ($this->supportsForeignKeyConstraints()) {
 foreach ($diff->removedForeignKeys as $foreignKey) {
 $sql[] = $this->getDropForeignKeySQL($foreignKey, $tableName);
 }
 foreach ($diff->changedForeignKeys as $foreignKey) {
 $sql[] = $this->getDropForeignKeySQL($foreignKey, $tableName);
 }
 }
 foreach ($diff->removedIndexes as $index) {
 $sql[] = $this->getDropIndexSQL($index, $tableName);
 }
 foreach ($diff->changedIndexes as $index) {
 $sql[] = $this->getDropIndexSQL($index, $tableName);
 }
 return $sql;
 }
 protected function getPostAlterTableIndexForeignKeySQL(TableDiff $diff)
 {
 $sql = [];
 $newName = $diff->getNewName();
 if ($newName !== \false) {
 $tableName = $newName->getQuotedName($this);
 } else {
 $tableName = $diff->getName($this)->getQuotedName($this);
 }
 if ($this->supportsForeignKeyConstraints()) {
 foreach ($diff->addedForeignKeys as $foreignKey) {
 $sql[] = $this->getCreateForeignKeySQL($foreignKey, $tableName);
 }
 foreach ($diff->changedForeignKeys as $foreignKey) {
 $sql[] = $this->getCreateForeignKeySQL($foreignKey, $tableName);
 }
 }
 foreach ($diff->addedIndexes as $index) {
 $sql[] = $this->getCreateIndexSQL($index, $tableName);
 }
 foreach ($diff->changedIndexes as $index) {
 $sql[] = $this->getCreateIndexSQL($index, $tableName);
 }
 foreach ($diff->renamedIndexes as $oldIndexName => $index) {
 $oldIndexName = new Identifier($oldIndexName);
 $sql = array_merge($sql, $this->getRenameIndexSQL($oldIndexName->getQuotedName($this), $index, $tableName));
 }
 return $sql;
 }
 protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
 {
 return [$this->getDropIndexSQL($oldIndexName, $tableName), $this->getCreateIndexSQL($index, $tableName)];
 }
 protected function _getAlterTableIndexForeignKeySQL(TableDiff $diff)
 {
 return array_merge($this->getPreAlterTableIndexForeignKeySQL($diff), $this->getPostAlterTableIndexForeignKeySQL($diff));
 }
 public function getColumnDeclarationListSQL(array $columns)
 {
 $declarations = [];
 foreach ($columns as $name => $column) {
 $declarations[] = $this->getColumnDeclarationSQL($name, $column);
 }
 return implode(', ', $declarations);
 }
 public function getColumnDeclarationSQL($name, array $column)
 {
 if (isset($column['columnDefinition'])) {
 $declaration = $this->getCustomTypeDeclarationSQL($column);
 } else {
 $default = $this->getDefaultValueDeclarationSQL($column);
 $charset = isset($column['charset']) && $column['charset'] ? ' ' . $this->getColumnCharsetDeclarationSQL($column['charset']) : '';
 $collation = isset($column['collation']) && $column['collation'] ? ' ' . $this->getColumnCollationDeclarationSQL($column['collation']) : '';
 $notnull = isset($column['notnull']) && $column['notnull'] ? ' NOT NULL' : '';
 $unique = isset($column['unique']) && $column['unique'] ? ' ' . $this->getUniqueFieldDeclarationSQL() : '';
 $check = isset($column['check']) && $column['check'] ? ' ' . $column['check'] : '';
 $typeDecl = $column['type']->getSQLDeclaration($column, $this);
 $declaration = $typeDecl . $charset . $default . $notnull . $unique . $check . $collation;
 if ($this->supportsInlineColumnComments() && isset($column['comment']) && $column['comment'] !== '') {
 $declaration .= ' ' . $this->getInlineColumnCommentSQL($column['comment']);
 }
 }
 return $name . ' ' . $declaration;
 }
 public function getDecimalTypeDeclarationSQL(array $column)
 {
 $column['precision'] = !isset($column['precision']) || empty($column['precision']) ? 10 : $column['precision'];
 $column['scale'] = !isset($column['scale']) || empty($column['scale']) ? 0 : $column['scale'];
 return 'NUMERIC(' . $column['precision'] . ', ' . $column['scale'] . ')';
 }
 public function getDefaultValueDeclarationSQL($column)
 {
 if (!isset($column['default'])) {
 return empty($column['notnull']) ? ' DEFAULT NULL' : '';
 }
 $default = $column['default'];
 if (!isset($column['type'])) {
 return " DEFAULT '" . $default . "'";
 }
 $type = $column['type'];
 if ($type instanceof Types\PhpIntegerMappingType) {
 return ' DEFAULT ' . $default;
 }
 if ($type instanceof Types\PhpDateTimeMappingType && $default === $this->getCurrentTimestampSQL()) {
 return ' DEFAULT ' . $this->getCurrentTimestampSQL();
 }
 if ($type instanceof Types\TimeType && $default === $this->getCurrentTimeSQL()) {
 return ' DEFAULT ' . $this->getCurrentTimeSQL();
 }
 if ($type instanceof Types\DateType && $default === $this->getCurrentDateSQL()) {
 return ' DEFAULT ' . $this->getCurrentDateSQL();
 }
 if ($type instanceof Types\BooleanType) {
 return " DEFAULT '" . $this->convertBooleans($default) . "'";
 }
 return ' DEFAULT ' . $this->quoteStringLiteral($default);
 }
 public function getCheckDeclarationSQL(array $definition)
 {
 $constraints = [];
 foreach ($definition as $column => $def) {
 if (is_string($def)) {
 $constraints[] = 'CHECK (' . $def . ')';
 } else {
 if (isset($def['min'])) {
 $constraints[] = 'CHECK (' . $column . ' >= ' . $def['min'] . ')';
 }
 if (isset($def['max'])) {
 $constraints[] = 'CHECK (' . $column . ' <= ' . $def['max'] . ')';
 }
 }
 }
 return implode(', ', $constraints);
 }
 public function getUniqueConstraintDeclarationSQL($name, Index $index)
 {
 $columns = $index->getColumns();
 $name = new Identifier($name);
 if (count($columns) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'columns' required.");
 }
 return 'CONSTRAINT ' . $name->getQuotedName($this) . ' UNIQUE (' . $this->getIndexFieldDeclarationListSQL($index) . ')' . $this->getPartialIndexSQL($index);
 }
 public function getIndexDeclarationSQL($name, Index $index)
 {
 $columns = $index->getColumns();
 $name = new Identifier($name);
 if (count($columns) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'columns' required.");
 }
 return $this->getCreateIndexSQLFlags($index) . 'INDEX ' . $name->getQuotedName($this) . ' (' . $this->getIndexFieldDeclarationListSQL($index) . ')' . $this->getPartialIndexSQL($index);
 }
 public function getCustomTypeDeclarationSQL(array $column)
 {
 return $column['columnDefinition'];
 }
 public function getIndexFieldDeclarationListSQL($columnsOrIndex) : string
 {
 if ($columnsOrIndex instanceof Index) {
 return implode(', ', $columnsOrIndex->getQuotedColumns($this));
 }
 if (!is_array($columnsOrIndex)) {
 throw new InvalidArgumentException('Fields argument should be an Index or array.');
 }
 $ret = [];
 foreach ($columnsOrIndex as $column => $definition) {
 if (is_array($definition)) {
 $ret[] = $column;
 } else {
 $ret[] = $definition;
 }
 }
 return implode(', ', $ret);
 }
 public function getTemporaryTableSQL()
 {
 return 'TEMPORARY';
 }
 public function getTemporaryTableName($tableName)
 {
 return $tableName;
 }
 public function getForeignKeyDeclarationSQL(ForeignKeyConstraint $foreignKey)
 {
 $sql = $this->getForeignKeyBaseDeclarationSQL($foreignKey);
 $sql .= $this->getAdvancedForeignKeyOptionsSQL($foreignKey);
 return $sql;
 }
 public function getAdvancedForeignKeyOptionsSQL(ForeignKeyConstraint $foreignKey)
 {
 $query = '';
 if ($this->supportsForeignKeyOnUpdate() && $foreignKey->hasOption('onUpdate')) {
 $query .= ' ON UPDATE ' . $this->getForeignKeyReferentialActionSQL($foreignKey->getOption('onUpdate'));
 }
 if ($foreignKey->hasOption('onDelete')) {
 $query .= ' ON DELETE ' . $this->getForeignKeyReferentialActionSQL($foreignKey->getOption('onDelete'));
 }
 return $query;
 }
 public function getForeignKeyReferentialActionSQL($action)
 {
 $upper = strtoupper($action);
 switch ($upper) {
 case 'CASCADE':
 case 'SET NULL':
 case 'NO ACTION':
 case 'RESTRICT':
 case 'SET DEFAULT':
 return $upper;
 default:
 throw new InvalidArgumentException('Invalid foreign key action: ' . $upper);
 }
 }
 public function getForeignKeyBaseDeclarationSQL(ForeignKeyConstraint $foreignKey)
 {
 $sql = '';
 if (strlen($foreignKey->getName())) {
 $sql .= 'CONSTRAINT ' . $foreignKey->getQuotedName($this) . ' ';
 }
 $sql .= 'FOREIGN KEY (';
 if (count($foreignKey->getLocalColumns()) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'local' required.");
 }
 if (count($foreignKey->getForeignColumns()) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'foreign' required.");
 }
 if (strlen($foreignKey->getForeignTableName()) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'foreignTable' required.");
 }
 return $sql . implode(', ', $foreignKey->getQuotedLocalColumns($this)) . ') REFERENCES ' . $foreignKey->getQuotedForeignTableName($this) . ' (' . implode(', ', $foreignKey->getQuotedForeignColumns($this)) . ')';
 }
 public function getUniqueFieldDeclarationSQL()
 {
 return 'UNIQUE';
 }
 public function getColumnCharsetDeclarationSQL($charset)
 {
 return '';
 }
 public function getColumnCollationDeclarationSQL($collation)
 {
 return $this->supportsColumnCollation() ? 'COLLATE ' . $collation : '';
 }
 public function prefersSequences()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4229', 'AbstractPlatform::prefersSequences() is deprecated without replacement and removed in DBAL 3.0');
 return \false;
 }
 public function prefersIdentityColumns()
 {
 return \false;
 }
 public function convertBooleans($item)
 {
 if (is_array($item)) {
 foreach ($item as $k => $value) {
 if (!is_bool($value)) {
 continue;
 }
 $item[$k] = (int) $value;
 }
 } elseif (is_bool($item)) {
 $item = (int) $item;
 }
 return $item;
 }
 public function convertFromBoolean($item)
 {
 return $item === null ? null : (bool) $item;
 }
 public function convertBooleansToDatabaseValue($item)
 {
 return $this->convertBooleans($item);
 }
 public function getCurrentDateSQL()
 {
 return 'CURRENT_DATE';
 }
 public function getCurrentTimeSQL()
 {
 return 'CURRENT_TIME';
 }
 public function getCurrentTimestampSQL()
 {
 return 'CURRENT_TIMESTAMP';
 }
 protected function _getTransactionIsolationLevelSQL($level)
 {
 switch ($level) {
 case TransactionIsolationLevel::READ_UNCOMMITTED:
 return 'READ UNCOMMITTED';
 case TransactionIsolationLevel::READ_COMMITTED:
 return 'READ COMMITTED';
 case TransactionIsolationLevel::REPEATABLE_READ:
 return 'REPEATABLE READ';
 case TransactionIsolationLevel::SERIALIZABLE:
 return 'SERIALIZABLE';
 default:
 throw new InvalidArgumentException('Invalid isolation level:' . $level);
 }
 }
 public function getListDatabasesSQL()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListNamespacesSQL()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListSequencesSQL($database)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListTableConstraintsSQL($table)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListTableColumnsSQL($table, $database = null)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListTablesSQL()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListUsersSQL()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListViewsSQL($database)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListTableIndexesSQL($table, $database = null)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getListTableForeignKeysSQL($table)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getCreateViewSQL($name, $sql)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDropViewSQL($name)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDropSequenceSQL($sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getSequenceNextValSQL($sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getCreateDatabaseSQL($name)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getSetTransactionIsolationSQL($level)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDateTimeTypeDeclarationSQL(array $column)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDateTimeTzTypeDeclarationSQL(array $column)
 {
 return $this->getDateTimeTypeDeclarationSQL($column);
 }
 public function getDateTypeDeclarationSQL(array $column)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getTimeTypeDeclarationSQL(array $column)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getFloatDeclarationSQL(array $column)
 {
 return 'DOUBLE PRECISION';
 }
 public function getDefaultTransactionIsolationLevel()
 {
 return TransactionIsolationLevel::READ_COMMITTED;
 }
 public function supportsSequences()
 {
 return \false;
 }
 public function supportsIdentityColumns()
 {
 return \false;
 }
 public function usesSequenceEmulatedIdentityColumns()
 {
 return \false;
 }
 public function getIdentitySequenceName($tableName, $columnName)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function supportsIndexes()
 {
 return \true;
 }
 public function supportsPartialIndexes()
 {
 return \false;
 }
 public function supportsColumnLengthIndexes() : bool
 {
 return \false;
 }
 public function supportsAlterTable()
 {
 return \true;
 }
 public function supportsTransactions()
 {
 return \true;
 }
 public function supportsSavepoints()
 {
 return \true;
 }
 public function supportsReleaseSavepoints()
 {
 return $this->supportsSavepoints();
 }
 public function supportsPrimaryConstraints()
 {
 return \true;
 }
 public function supportsForeignKeyConstraints()
 {
 return \true;
 }
 public function supportsForeignKeyOnUpdate()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4229', 'AbstractPlatform::supportsForeignKeyOnUpdate() is deprecated without replacement and removed in DBAL 3.0');
 return $this->supportsForeignKeyConstraints();
 }
 public function supportsSchemas()
 {
 return \false;
 }
 public function canEmulateSchemas()
 {
 return \false;
 }
 public function getDefaultSchemaName()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function supportsCreateDropDatabase()
 {
 return \true;
 }
 public function supportsGettingAffectedRows()
 {
 return \true;
 }
 public function supportsInlineColumnComments()
 {
 return \false;
 }
 public function supportsCommentOnStatement()
 {
 return \false;
 }
 public function hasNativeGuidType()
 {
 return \false;
 }
 public function hasNativeJsonType()
 {
 return \false;
 }
 public function getIdentityColumnNullInsertSQL()
 {
 return '';
 }
 public function supportsViews()
 {
 return \true;
 }
 public function supportsColumnCollation()
 {
 return \false;
 }
 public function getDateTimeFormatString()
 {
 return 'Y-m-d H:i:s';
 }
 public function getDateTimeTzFormatString()
 {
 return 'Y-m-d H:i:s';
 }
 public function getDateFormatString()
 {
 return 'Y-m-d';
 }
 public function getTimeFormatString()
 {
 return 'H:i:s';
 }
 public final function modifyLimitQuery($query, $limit, $offset = null)
 {
 if ($limit !== null) {
 $limit = (int) $limit;
 }
 $offset = (int) $offset;
 if ($offset < 0) {
 throw new Exception(sprintf('Offset must be a positive integer or zero, %d given', $offset));
 }
 if ($offset > 0 && !$this->supportsLimitOffset()) {
 throw new Exception(sprintf('Platform %s does not support offset values in limit queries.', $this->getName()));
 }
 return $this->doModifyLimitQuery($query, $limit, $offset);
 }
 protected function doModifyLimitQuery($query, $limit, $offset)
 {
 if ($limit !== null) {
 $query .= ' LIMIT ' . $limit;
 }
 if ($offset > 0) {
 $query .= ' OFFSET ' . $offset;
 }
 return $query;
 }
 public function supportsLimitOffset()
 {
 return \true;
 }
 public function getSQLResultCasing($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4229', 'AbstractPlatform::getSQLResultCasing is deprecated without replacement and removed in DBAL 3.' . 'Use Portability\\Connection with PORTABILITY_FIX_CASE to get portable result cases.');
 return $column;
 }
 public function fixSchemaElementName($schemaElementName)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4132', 'AbstractPlatform::fixSchemaElementName is deprecated with no replacement and removed in DBAL 3.0');
 return $schemaElementName;
 }
 public function getMaxIdentifierLength()
 {
 return 63;
 }
 public function getEmptyIdentityInsertSQL($quotedTableName, $quotedIdentifierColumnName)
 {
 return 'INSERT INTO ' . $quotedTableName . ' (' . $quotedIdentifierColumnName . ') VALUES (null)';
 }
 public function getTruncateTableSQL($tableName, $cascade = \false)
 {
 $tableIdentifier = new Identifier($tableName);
 return 'TRUNCATE ' . $tableIdentifier->getQuotedName($this);
 }
 public function getDummySelectSQL()
 {
 $expression = func_num_args() > 0 ? func_get_arg(0) : '1';
 return sprintf('SELECT %s', $expression);
 }
 public function createSavePoint($savepoint)
 {
 return 'SAVEPOINT ' . $savepoint;
 }
 public function releaseSavePoint($savepoint)
 {
 return 'RELEASE SAVEPOINT ' . $savepoint;
 }
 public function rollbackSavePoint($savepoint)
 {
 return 'ROLLBACK TO SAVEPOINT ' . $savepoint;
 }
 public final function getReservedKeywordsList()
 {
 // Check for an existing instantiation of the keywords class.
 if ($this->_keywords) {
 return $this->_keywords;
 }
 $class = $this->getReservedKeywordsClass();
 $keywords = new $class();
 if (!$keywords instanceof KeywordList) {
 throw Exception::notSupported(__METHOD__);
 }
 // Store the instance so it doesn't need to be generated on every request.
 $this->_keywords = $keywords;
 return $keywords;
 }
 protected function getReservedKeywordsClass()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function quoteStringLiteral($str)
 {
 $c = $this->getStringLiteralQuoteCharacter();
 return $c . str_replace($c, $c . $c, $str) . $c;
 }
 public function getStringLiteralQuoteCharacter()
 {
 return "'";
 }
 public final function escapeStringForLike(string $inputString, string $escapeChar) : string
 {
 return preg_replace('~([' . preg_quote($this->getLikeWildcardCharacters() . $escapeChar, '~') . '])~u', addcslashes($escapeChar, '\\') . '$1', $inputString);
 }
 protected function getLikeWildcardCharacters() : string
 {
 return '%_';
 }
}
