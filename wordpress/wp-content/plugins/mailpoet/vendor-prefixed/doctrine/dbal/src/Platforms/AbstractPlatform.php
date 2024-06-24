<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Connection;
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
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidLockMode;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\DBAL\Platforms\Keywords\KeywordList;
use MailPoetVendor\Doctrine\DBAL\Schema\AbstractSchemaManager;
use MailPoetVendor\Doctrine\DBAL\Schema\Column;
use MailPoetVendor\Doctrine\DBAL\Schema\ColumnDiff;
use MailPoetVendor\Doctrine\DBAL\Schema\Constraint;
use MailPoetVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use MailPoetVendor\Doctrine\DBAL\Schema\Identifier;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaDiff;
use MailPoetVendor\Doctrine\DBAL\Schema\Sequence;
use MailPoetVendor\Doctrine\DBAL\Schema\Table;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
use MailPoetVendor\Doctrine\DBAL\Schema\UniqueConstraint;
use MailPoetVendor\Doctrine\DBAL\SQL\Builder\DefaultSelectSQLBuilder;
use MailPoetVendor\Doctrine\DBAL\SQL\Builder\SelectSQLBuilder;
use MailPoetVendor\Doctrine\DBAL\SQL\Parser;
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
 protected $doctrineTypeMapping;
 protected $doctrineTypeComments;
 protected $_eventManager;
 protected $_keywords;
 private bool $disableTypeComments = \false;
 public final function setDisableTypeComments(bool $value) : void
 {
 $this->disableTypeComments = $value;
 }
 public function setEventManager(EventManager $eventManager)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', '%s is deprecated.', __METHOD__);
 $this->_eventManager = $eventManager;
 }
 public function getEventManager()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', '%s is deprecated.', __METHOD__);
 return $this->_eventManager;
 }
 public abstract function getBooleanTypeDeclarationSQL(array $column);
 public abstract function getIntegerTypeDeclarationSQL(array $column);
 public abstract function getBigIntTypeDeclarationSQL(array $column);
 public abstract function getSmallIntTypeDeclarationSQL(array $column);
 protected abstract function _getCommonIntegerTypeDeclarationSQL(array $column);
 protected abstract function initializeDoctrineTypeMappings();
 private function initializeAllDoctrineTypeMappings() : void
 {
 $this->initializeDoctrineTypeMappings();
 foreach (Type::getTypesMap() as $typeName => $className) {
 foreach (Type::getType($typeName)->getMappedDatabaseTypes($this) as $dbType) {
 $dbType = strtolower($dbType);
 $this->doctrineTypeMapping[$dbType] = $typeName;
 }
 }
 }
 public function getAsciiStringTypeDeclarationSQL(array $column) : string
 {
 return $this->getStringTypeDeclarationSQL($column);
 }
 public function getVarcharTypeDeclarationSQL(array $column)
 {
 if (isset($column['length'])) {
 $lengthOmitted = \false;
 } else {
 $column['length'] = $this->getVarcharDefaultLength();
 $lengthOmitted = \true;
 }
 $fixed = $column['fixed'] ?? \false;
 $maxLength = $fixed ? $this->getCharMaxLength() : $this->getVarcharMaxLength();
 if ($column['length'] > $maxLength) {
 return $this->getClobTypeDeclarationSQL($column);
 }
 return $this->getVarcharTypeDeclarationSQLSnippet($column['length'], $fixed, $lengthOmitted);
 }
 public function getStringTypeDeclarationSQL(array $column)
 {
 return $this->getVarcharTypeDeclarationSQL($column);
 }
 public function getBinaryTypeDeclarationSQL(array $column)
 {
 if (isset($column['length'])) {
 $lengthOmitted = \false;
 } else {
 $column['length'] = $this->getBinaryDefaultLength();
 $lengthOmitted = \true;
 }
 $fixed = $column['fixed'] ?? \false;
 $maxLength = $this->getBinaryMaxLength();
 if ($column['length'] > $maxLength) {
 if ($maxLength > 0) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3187', 'Binary column length %d is greater than supported by the platform (%d).' . ' Reduce the column length or use a BLOB column instead.', $column['length'], $maxLength);
 }
 return $this->getBlobTypeDeclarationSQL($column);
 }
 return $this->getBinaryTypeDeclarationSQLSnippet($column['length'], $fixed, $lengthOmitted);
 }
 public function getGuidTypeDeclarationSQL(array $column)
 {
 $column['length'] = 36;
 $column['fixed'] = \true;
 return $this->getStringTypeDeclarationSQL($column);
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5058', '%s is deprecated and will be removed in Doctrine DBAL 4.0.', __METHOD__);
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5058', '%s is deprecated and will be removed in Doctrine DBAL 4.0. Use Type::requiresSQLCommentHint() instead.', __METHOD__);
 if ($this->doctrineTypeComments === null) {
 $this->initializeCommentedDoctrineTypes();
 }
 return $doctrineType->requiresSQLCommentHint($this);
 }
 public function markDoctrineTypeCommented($doctrineType)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5058', '%s is deprecated and will be removed in Doctrine DBAL 4.0. Use Type::requiresSQLCommentHint() instead.', __METHOD__);
 if ($this->doctrineTypeComments === null) {
 $this->initializeCommentedDoctrineTypes();
 }
 assert(is_array($this->doctrineTypeComments));
 $this->doctrineTypeComments[] = $doctrineType instanceof Type ? $doctrineType->getName() : $doctrineType;
 }
 public function getDoctrineTypeComment(Type $doctrineType)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5107', '%s is deprecated and will be removed in Doctrine DBAL 4.0.', __METHOD__);
 return '(DC2Type:' . $doctrineType->getName() . ')';
 }
 protected function getColumnComment(Column $column)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5107', '%s is deprecated and will be removed in Doctrine DBAL 4.0.', __METHOD__);
 $comment = $column->getComment();
 if (!$this->disableTypeComments && $column->getType()->requiresSQLCommentHint($this)) {
 $comment .= $this->getDoctrineTypeComment($column->getType());
 }
 return $comment;
 }
 public function getIdentifierQuoteCharacter()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5388', 'AbstractPlatform::getIdentifierQuoteCharacter() is deprecated. Use quoteIdentifier() instead.');
 return '"';
 }
 public function getSqlCommentStartString()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getSqlCommentStartString() is deprecated.');
 return '--';
 }
 public function getSqlCommentEndString()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getSqlCommentEndString() is deprecated.');
 return "\n";
 }
 public function getCharMaxLength() : int
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3263', 'AbstractPlatform::getCharMaxLength() is deprecated.');
 return $this->getVarcharMaxLength();
 }
 public function getVarcharMaxLength()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3263', 'AbstractPlatform::getVarcharMaxLength() is deprecated.');
 return 4000;
 }
 public function getVarcharDefaultLength()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3263', 'Relying on the default varchar column length is deprecated, specify the length explicitly.');
 return 255;
 }
 public function getBinaryMaxLength()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3263', 'AbstractPlatform::getBinaryMaxLength() is deprecated.');
 return 4000;
 }
 public function getBinaryDefaultLength()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3263', 'Relying on the default binary column length is deprecated, specify the length explicitly.');
 return 255;
 }
 public function getWildcards()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getWildcards() is deprecated.' . ' Use AbstractPlatform::getLikeWildcardCharacters() instead.');
 return ['%', '_'];
 }
 public function getRegexpExpression()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getAvgExpression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getAvgExpression() is deprecated. Use AVG() in SQL instead.');
 return 'AVG(' . $column . ')';
 }
 public function getCountExpression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getCountExpression() is deprecated. Use COUNT() in SQL instead.');
 return 'COUNT(' . $column . ')';
 }
 public function getMaxExpression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getMaxExpression() is deprecated. Use MAX() in SQL instead.');
 return 'MAX(' . $column . ')';
 }
 public function getMinExpression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getMinExpression() is deprecated. Use MIN() in SQL instead.');
 return 'MIN(' . $column . ')';
 }
 public function getSumExpression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getSumExpression() is deprecated. Use SUM() in SQL instead.');
 return 'SUM(' . $column . ')';
 }
 // scalar functions
 public function getMd5Expression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getMd5Expression() is deprecated.');
 return 'MD5(' . $column . ')';
 }
 public function getLengthExpression($column)
 {
 return 'LENGTH(' . $column . ')';
 }
 public function getSqrtExpression($column)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getSqrtExpression() is deprecated. Use SQRT() in SQL instead.');
 return 'SQRT(' . $column . ')';
 }
 public function getRoundExpression($column, $decimals = 0)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getRoundExpression() is deprecated. Use ROUND() in SQL instead.');
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
 if ($mode !== TrimMode::UNSPECIFIED || $char !== \false) {
 $expression .= 'FROM ';
 }
 return 'TRIM(' . $expression . $str . ')';
 }
 public function getRtrimExpression($str)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getRtrimExpression() is deprecated. Use RTRIM() in SQL instead.');
 return 'RTRIM(' . $str . ')';
 }
 public function getLtrimExpression($str)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getLtrimExpression() is deprecated. Use LTRIM() in SQL instead.');
 return 'LTRIM(' . $str . ')';
 }
 public function getUpperExpression($str)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getUpperExpression() is deprecated. Use UPPER() in SQL instead.');
 return 'UPPER(' . $str . ')';
 }
 public function getLowerExpression($str)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getLowerExpression() is deprecated. Use LOWER() in SQL instead.');
 return 'LOWER(' . $str . ')';
 }
 public function getLocateExpression($str, $substr, $startPos = \false)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getNowExpression()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4753', 'AbstractPlatform::getNowExpression() is deprecated. Generate dates within the application.');
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getNotExpression() is deprecated. Use NOT() in SQL instead.');
 return 'NOT(' . $expression . ')';
 }
 public function getIsNullExpression($expression)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getIsNullExpression() is deprecated. Use IS NULL in SQL instead.');
 return $expression . ' IS NULL';
 }
 public function getIsNotNullExpression($expression)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getIsNotNullExpression() is deprecated. Use IS NOT NULL in SQL instead.');
 return $expression . ' IS NOT NULL';
 }
 public function getBetweenExpression($expression, $value1, $value2)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getBetweenExpression() is deprecated. Use BETWEEN in SQL instead.');
 return $expression . ' BETWEEN ' . $value1 . ' AND ' . $value2;
 }
 public function getAcosExpression($value)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getAcosExpression() is deprecated. Use ACOS() in SQL instead.');
 return 'ACOS(' . $value . ')';
 }
 public function getSinExpression($value)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getSinExpression() is deprecated. Use SIN() in SQL instead.');
 return 'SIN(' . $value . ')';
 }
 public function getPiExpression()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getPiExpression() is deprecated. Use PI() in SQL instead.');
 return 'PI()';
 }
 public function getCosExpression($value)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getCosExpression() is deprecated. Use COS() in SQL instead.');
 return 'COS(' . $value . ')';
 }
 public function getDateDiffExpression($date1, $date2)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDateAddSecondsExpression($date, $seconds)
 {
 if (is_int($seconds)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $seconds as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $seconds, DateIntervalUnit::SECOND);
 }
 public function getDateSubSecondsExpression($date, $seconds)
 {
 if (is_int($seconds)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $seconds as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $seconds, DateIntervalUnit::SECOND);
 }
 public function getDateAddMinutesExpression($date, $minutes)
 {
 if (is_int($minutes)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $minutes as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $minutes, DateIntervalUnit::MINUTE);
 }
 public function getDateSubMinutesExpression($date, $minutes)
 {
 if (is_int($minutes)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $minutes as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $minutes, DateIntervalUnit::MINUTE);
 }
 public function getDateAddHourExpression($date, $hours)
 {
 if (is_int($hours)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $hours as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $hours, DateIntervalUnit::HOUR);
 }
 public function getDateSubHourExpression($date, $hours)
 {
 if (is_int($hours)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $hours as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $hours, DateIntervalUnit::HOUR);
 }
 public function getDateAddDaysExpression($date, $days)
 {
 if (is_int($days)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $days as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $days, DateIntervalUnit::DAY);
 }
 public function getDateSubDaysExpression($date, $days)
 {
 if (is_int($days)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $days as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $days, DateIntervalUnit::DAY);
 }
 public function getDateAddWeeksExpression($date, $weeks)
 {
 if (is_int($weeks)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $weeks as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $weeks, DateIntervalUnit::WEEK);
 }
 public function getDateSubWeeksExpression($date, $weeks)
 {
 if (is_int($weeks)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $weeks as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $weeks, DateIntervalUnit::WEEK);
 }
 public function getDateAddMonthExpression($date, $months)
 {
 if (is_int($months)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $months as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $months, DateIntervalUnit::MONTH);
 }
 public function getDateSubMonthExpression($date, $months)
 {
 if (is_int($months)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $months as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $months, DateIntervalUnit::MONTH);
 }
 public function getDateAddQuartersExpression($date, $quarters)
 {
 if (is_int($quarters)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $quarters as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $quarters, DateIntervalUnit::QUARTER);
 }
 public function getDateSubQuartersExpression($date, $quarters)
 {
 if (is_int($quarters)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $quarters as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $quarters, DateIntervalUnit::QUARTER);
 }
 public function getDateAddYearsExpression($date, $years)
 {
 if (is_int($years)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $years as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '+', $years, DateIntervalUnit::YEAR);
 }
 public function getDateSubYearsExpression($date, $years)
 {
 if (is_int($years)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3498', 'Passing $years as an integer is deprecated. Pass it as a numeric string instead.');
 }
 return $this->getDateArithmeticIntervalExpression($date, '-', $years, DateIntervalUnit::YEAR);
 }
 protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit)
 {
 throw Exception::notSupported(__METHOD__);
 }
 protected function multiplyInterval(string $interval, int $multiplier) : string
 {
 return sprintf('(%s * %d)', $interval, $multiplier);
 }
 public function getBitAndComparisonExpression($value1, $value2)
 {
 return '(' . $value1 . ' & ' . $value2 . ')';
 }
 public function getBitOrComparisonExpression($value1, $value2)
 {
 return '(' . $value1 . ' | ' . $value2 . ')';
 }
 public abstract function getCurrentDatabaseExpression() : string;
 public function getForUpdateSQL()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6191', '%s is deprecated as non-portable.', __METHOD__);
 return 'FOR UPDATE';
 }
 public function appendLockHint(string $fromClause, int $lockMode) : string
 {
 switch ($lockMode) {
 case LockMode::NONE:
 case LockMode::OPTIMISTIC:
 case LockMode::PESSIMISTIC_READ:
 case LockMode::PESSIMISTIC_WRITE:
 return $fromClause;
 default:
 throw InvalidLockMode::fromLockMode($lockMode);
 }
 }
 public function getReadLockSQL()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6191', '%s is deprecated as non-portable.', __METHOD__);
 return $this->getForUpdateSQL();
 }
 public function getWriteLockSQL()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6191', '%s is deprecated as non-portable.', __METHOD__);
 return $this->getForUpdateSQL();
 }
 public function getDropTableSQL($table)
 {
 $tableArg = $table;
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this);
 }
 if (!is_string($table)) {
 throw new InvalidArgumentException(__METHOD__ . '() expects $table parameter to be string or ' . Table::class . '.');
 }
 if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaDropTable)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaDropTable);
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
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this);
 }
 return $this->getDropTableSQL($table);
 }
 public function getDropIndexSQL($index, $table = null)
 {
 if ($index instanceof Index) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $index as an Index object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $index = $index->getQuotedName($this);
 } elseif (!is_string($index)) {
 throw new InvalidArgumentException(__METHOD__ . '() expects $index parameter to be string or ' . Index::class . '.');
 }
 return 'DROP INDEX ' . $index;
 }
 public function getDropConstraintSQL($constraint, $table)
 {
 if ($constraint instanceof Constraint) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $constraint as a Constraint object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 } else {
 $constraint = new Identifier($constraint);
 }
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 } else {
 $table = new Identifier($table);
 }
 $constraint = $constraint->getQuotedName($this);
 $table = $table->getQuotedName($this);
 return 'ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $constraint;
 }
 public function getDropForeignKeySQL($foreignKey, $table)
 {
 if ($foreignKey instanceof ForeignKeyConstraint) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $foreignKey as a ForeignKeyConstraint object to %s is deprecated.' . ' Pass it as a quoted name instead.', __METHOD__);
 } else {
 $foreignKey = new Identifier($foreignKey);
 }
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 } else {
 $table = new Identifier($table);
 }
 $foreignKey = $foreignKey->getQuotedName($this);
 $table = $table->getQuotedName($this);
 return 'ALTER TABLE ' . $table . ' DROP FOREIGN KEY ' . $foreignKey;
 }
 public function getDropUniqueConstraintSQL(string $name, string $tableName) : string
 {
 return $this->getDropConstraintSQL($name, $tableName);
 }
 public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES)
 {
 if (!is_int($createFlags)) {
 throw new InvalidArgumentException('Second argument of AbstractPlatform::getCreateTableSQL() has to be integer.');
 }
 if (($createFlags & self::CREATE_INDEXES) === 0) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5416', 'Unsetting the CREATE_INDEXES flag in AbstractPlatform::getCreateTableSQL() is deprecated.');
 }
 if (($createFlags & self::CREATE_FOREIGNKEYS) === 0) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5416', 'Not setting the CREATE_FOREIGNKEYS flag in AbstractPlatform::getCreateTableSQL()' . ' is deprecated. In order to build the statements that create multiple tables' . ' referencing each other via foreign keys, use AbstractPlatform::getCreateTablesSQL().');
 }
 return $this->buildCreateTableSQL($table, ($createFlags & self::CREATE_INDEXES) > 0, ($createFlags & self::CREATE_FOREIGNKEYS) > 0);
 }
 public function createSelectSQLBuilder() : SelectSQLBuilder
 {
 return new DefaultSelectSQLBuilder($this, 'FOR UPDATE', 'SKIP LOCKED');
 }
 protected final function getCreateTableWithoutForeignKeysSQL(Table $table) : array
 {
 return $this->buildCreateTableSQL($table, \true, \false);
 }
 private function buildCreateTableSQL(Table $table, bool $createIndexes, bool $createForeignKeys) : array
 {
 if (count($table->getColumns()) === 0) {
 throw Exception::noColumnsSpecifiedForTable($table->getName());
 }
 $tableName = $table->getQuotedName($this);
 $options = $table->getOptions();
 $options['uniqueConstraints'] = [];
 $options['indexes'] = [];
 $options['primary'] = [];
 if ($createIndexes) {
 foreach ($table->getIndexes() as $index) {
 if (!$index->isPrimary()) {
 $options['indexes'][$index->getQuotedName($this)] = $index;
 continue;
 }
 $options['primary'] = $index->getQuotedColumns($this);
 $options['primary_index'] = $index;
 }
 foreach ($table->getUniqueConstraints() as $uniqueConstraint) {
 $options['uniqueConstraints'][$uniqueConstraint->getQuotedName($this)] = $uniqueConstraint;
 }
 }
 if ($createForeignKeys) {
 $options['foreignKeys'] = [];
 foreach ($table->getForeignKeys() as $fkConstraint) {
 $options['foreignKeys'][] = $fkConstraint;
 }
 }
 $columnSql = [];
 $columns = [];
 foreach ($table->getColumns() as $column) {
 if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaCreateTableColumn)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaCreateTableColumn);
 $eventArgs = new SchemaCreateTableColumnEventArgs($column, $table, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaCreateTableColumn, $eventArgs);
 $columnSql = array_merge($columnSql, $eventArgs->getSql());
 if ($eventArgs->isDefaultPrevented()) {
 continue;
 }
 }
 $columnData = $this->columnToArray($column);
 if (in_array($column->getName(), $options['primary'], \true)) {
 $columnData['primary'] = \true;
 }
 $columns[$columnData['name']] = $columnData;
 }
 if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaCreateTable)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaCreateTable);
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
 public function getCreateTablesSQL(array $tables) : array
 {
 $sql = [];
 foreach ($tables as $table) {
 $sql = array_merge($sql, $this->getCreateTableWithoutForeignKeysSQL($table));
 }
 foreach ($tables as $table) {
 foreach ($table->getForeignKeys() as $foreignKey) {
 $sql[] = $this->getCreateForeignKeySQL($foreignKey, $table->getQuotedName($this));
 }
 }
 return $sql;
 }
 public function getDropTablesSQL(array $tables) : array
 {
 $sql = [];
 foreach ($tables as $table) {
 foreach ($table->getForeignKeys() as $foreignKey) {
 $sql[] = $this->getDropForeignKeySQL($foreignKey->getQuotedName($this), $table->getQuotedName($this));
 }
 }
 foreach ($tables as $table) {
 $sql[] = $this->getDropTableSQL($table->getQuotedName($this));
 }
 return $sql;
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
 foreach ($options['foreignKeys'] as $definition) {
 $sql[] = $this->getCreateForeignKeySQL($definition, $name);
 }
 }
 return $sql;
 }
 public function getCreateTemporaryTableSnippetSQL()
 {
 return 'CREATE TEMPORARY TABLE';
 }
 public function getAlterSchemaSQL(SchemaDiff $diff) : array
 {
 return $diff->toSql($this);
 }
 public function getCreateSequenceSQL(Sequence $sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getAlterSequenceSQL(Sequence $sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getDropSequenceSQL($sequence)
 {
 if (!$this->supportsSequences()) {
 throw Exception::notSupported(__METHOD__);
 }
 if ($sequence instanceof Sequence) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $sequence as a Sequence object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $sequence = $sequence->getQuotedName($this);
 }
 return 'DROP SEQUENCE ' . $sequence;
 }
 public function getCreateConstraintSQL(Constraint $constraint, $table)
 {
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
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
 } elseif ($constraint instanceof UniqueConstraint) {
 $query .= ' UNIQUE';
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this);
 }
 $name = $index->getQuotedName($this);
 $columns = $index->getColumns();
 if (count($columns) === 0) {
 throw new InvalidArgumentException(sprintf('Incomplete or invalid index definition %s on table %s', $name, $table));
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this);
 }
 return 'ALTER TABLE ' . $table . ' ADD PRIMARY KEY (' . $this->getIndexFieldDeclarationListSQL($index) . ')';
 }
 public function getCreateSchemaSQL($schemaName)
 {
 if (!$this->supportsSchemas()) {
 throw Exception::notSupported(__METHOD__);
 }
 return 'CREATE SCHEMA ' . $schemaName;
 }
 public function getCreateUniqueConstraintSQL(UniqueConstraint $constraint, string $tableName) : string
 {
 return $this->getCreateConstraintSQL($constraint, $tableName);
 }
 public function getDropSchemaSQL(string $schemaName) : string
 {
 if (!$this->supportsSchemas()) {
 throw Exception::notSupported(__METHOD__);
 }
 return 'DROP SCHEMA ' . $schemaName;
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this);
 }
 return 'ALTER TABLE ' . $table . ' ADD ' . $this->getForeignKeyDeclarationSQL($foreignKey);
 }
 public function getAlterTableSQL(TableDiff $diff)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getRenameTableSQL(string $oldName, string $newName) : array
 {
 return [sprintf('ALTER TABLE %s RENAME TO %s', $oldName, $newName)];
 }
 protected function onSchemaAlterTableAddColumn(Column $column, TableDiff $diff, &$columnSql)
 {
 if ($this->_eventManager === null) {
 return \false;
 }
 if (!$this->_eventManager->hasListeners(Events::onSchemaAlterTableAddColumn)) {
 return \false;
 }
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaAlterTableAddColumn);
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaAlterTableRemoveColumn);
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaAlterTableChangeColumn);
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaAlterTableRenameColumn);
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated.', Events::onSchemaAlterTable);
 $eventArgs = new SchemaAlterTableEventArgs($diff, $this);
 $this->_eventManager->dispatchEvent(Events::onSchemaAlterTable, $eventArgs);
 $sql = array_merge($sql, $eventArgs->getSql());
 return $eventArgs->isDefaultPrevented();
 }
 protected function getPreAlterTableIndexForeignKeySQL(TableDiff $diff)
 {
 $tableNameSQL = ($diff->getOldTable() ?? $diff->getName($this))->getQuotedName($this);
 $sql = [];
 if ($this->supportsForeignKeyConstraints()) {
 foreach ($diff->getDroppedForeignKeys() as $foreignKey) {
 if ($foreignKey instanceof ForeignKeyConstraint) {
 $foreignKey = $foreignKey->getQuotedName($this);
 }
 $sql[] = $this->getDropForeignKeySQL($foreignKey, $tableNameSQL);
 }
 foreach ($diff->getModifiedForeignKeys() as $foreignKey) {
 $sql[] = $this->getDropForeignKeySQL($foreignKey->getQuotedName($this), $tableNameSQL);
 }
 }
 foreach ($diff->getDroppedIndexes() as $index) {
 $sql[] = $this->getDropIndexSQL($index->getQuotedName($this), $tableNameSQL);
 }
 foreach ($diff->getModifiedIndexes() as $index) {
 $sql[] = $this->getDropIndexSQL($index->getQuotedName($this), $tableNameSQL);
 }
 return $sql;
 }
 protected function getPostAlterTableIndexForeignKeySQL(TableDiff $diff)
 {
 $sql = [];
 $newName = $diff->getNewName();
 if ($newName !== \false) {
 $tableNameSQL = $newName->getQuotedName($this);
 } else {
 $tableNameSQL = ($diff->getOldTable() ?? $diff->getName($this))->getQuotedName($this);
 }
 if ($this->supportsForeignKeyConstraints()) {
 foreach ($diff->getAddedForeignKeys() as $foreignKey) {
 $sql[] = $this->getCreateForeignKeySQL($foreignKey, $tableNameSQL);
 }
 foreach ($diff->getModifiedForeignKeys() as $foreignKey) {
 $sql[] = $this->getCreateForeignKeySQL($foreignKey, $tableNameSQL);
 }
 }
 foreach ($diff->getAddedIndexes() as $index) {
 $sql[] = $this->getCreateIndexSQL($index, $tableNameSQL);
 }
 foreach ($diff->getModifiedIndexes() as $index) {
 $sql[] = $this->getCreateIndexSQL($index, $tableNameSQL);
 }
 foreach ($diff->getRenamedIndexes() as $oldIndexName => $index) {
 $oldIndexName = new Identifier($oldIndexName);
 $sql = array_merge($sql, $this->getRenameIndexSQL($oldIndexName->getQuotedName($this), $index, $tableNameSQL));
 }
 return $sql;
 }
 protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
 {
 return [$this->getDropIndexSQL($oldIndexName, $tableName), $this->getCreateIndexSQL($index, $tableName)];
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
 $charset = !empty($column['charset']) ? ' ' . $this->getColumnCharsetDeclarationSQL($column['charset']) : '';
 $collation = !empty($column['collation']) ? ' ' . $this->getColumnCollationDeclarationSQL($column['collation']) : '';
 $notnull = !empty($column['notnull']) ? ' NOT NULL' : '';
 if (!empty($column['unique'])) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5656', 'The usage of the "unique" column property is deprecated. Use unique constraints instead.');
 $unique = ' ' . $this->getUniqueFieldDeclarationSQL();
 } else {
 $unique = '';
 }
 if (!empty($column['check'])) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5656', 'The usage of the "check" column property is deprecated.');
 $check = ' ' . $column['check'];
 } else {
 $check = '';
 }
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
 if (empty($column['precision'])) {
 if (!isset($column['precision'])) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5637', 'Relying on the default decimal column precision is deprecated' . ', specify the precision explicitly.');
 }
 $precision = 10;
 } else {
 $precision = $column['precision'];
 }
 if (empty($column['scale'])) {
 if (!isset($column['scale'])) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5637', 'Relying on the default decimal column scale is deprecated' . ', specify the scale explicitly.');
 }
 $scale = 0;
 } else {
 $scale = $column['scale'];
 }
 return 'NUMERIC(' . $precision . ', ' . $scale . ')';
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
 return ' DEFAULT ' . $this->convertBooleans($default);
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
 public function getUniqueConstraintDeclarationSQL($name, UniqueConstraint $constraint)
 {
 $columns = $constraint->getQuotedColumns($this);
 $name = new Identifier($name);
 if (count($columns) === 0) {
 throw new InvalidArgumentException("Incomplete definition. 'columns' required.");
 }
 $constraintFlags = array_merge(['UNIQUE'], array_map('strtoupper', $constraint->getFlags()));
 $constraintName = $name->getQuotedName($this);
 $columnListNames = $this->getColumnsFieldDeclarationListSQL($columns);
 return sprintf('CONSTRAINT %s %s (%s)', $constraintName, implode(' ', $constraintFlags), $columnListNames);
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5527', '%s is deprecated.', __METHOD__);
 return $column['columnDefinition'];
 }
 public function getIndexFieldDeclarationListSQL(Index $index) : string
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5527', '%s is deprecated.', __METHOD__);
 return implode(', ', $index->getQuotedColumns($this));
 }
 public function getColumnsFieldDeclarationListSQL(array $columns) : string
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5527', '%s is deprecated.', __METHOD__);
 $ret = [];
 foreach ($columns as $column => $definition) {
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getTemporaryTableSQL() is deprecated.');
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
 if ($foreignKey->hasOption('onUpdate')) {
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
 if (strlen($foreignKey->getName()) > 0) {
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getUniqueFieldDeclarationSQL() is deprecated. Use UNIQUE in SQL instead.');
 return 'UNIQUE';
 }
 public function getColumnCharsetDeclarationSQL($charset)
 {
 return '';
 }
 public function getColumnCollationDeclarationSQL($collation)
 {
 return $this->supportsColumnCollation() ? 'COLLATE ' . $this->quoteSingleIdentifier($collation) : '';
 }
 public function prefersIdentityColumns()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/1519', 'AbstractPlatform::prefersIdentityColumns() is deprecated.');
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4503', 'AbstractPlatform::getListNamespacesSQL() is deprecated,' . ' use AbstractSchemaManager::listSchemaNames() instead.');
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::getListUsersSQL() is deprecated.');
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
 return 'CREATE VIEW ' . $name . ' AS ' . $sql;
 }
 public function getDropViewSQL($name)
 {
 return 'DROP VIEW ' . $name;
 }
 public function getSequenceNextValSQL($sequence)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function getCreateDatabaseSQL($name)
 {
 if (!$this->supportsCreateDropDatabase()) {
 throw Exception::notSupported(__METHOD__);
 }
 return 'CREATE DATABASE ' . $name;
 }
 public function getDropDatabaseSQL($name)
 {
 if (!$this->supportsCreateDropDatabase()) {
 throw Exception::notSupported(__METHOD__);
 }
 return 'DROP DATABASE ' . $name;
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5513', '%s is deprecated.', __METHOD__);
 return \false;
 }
 public function getIdentitySequenceName($tableName, $columnName)
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function supportsIndexes()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsIndexes() is deprecated.');
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsAlterTable() is deprecated. All platforms must implement altering tables.');
 return \true;
 }
 public function supportsTransactions()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsTransactions() is deprecated.');
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
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsPrimaryConstraints() is deprecated.');
 return \true;
 }
 public function supportsForeignKeyConstraints()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5409', 'AbstractPlatform::supportsForeignKeyConstraints() is deprecated.');
 return \true;
 }
 public function supportsSchemas()
 {
 return \false;
 }
 public function canEmulateSchemas()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4805', 'AbstractPlatform::canEmulateSchemas() is deprecated.');
 return \false;
 }
 public function getDefaultSchemaName()
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function supportsCreateDropDatabase()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5513', '%s is deprecated.', __METHOD__);
 return \true;
 }
 public function supportsGettingAffectedRows()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsGettingAffectedRows() is deprecated.');
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return \false;
 }
 public function hasNativeJsonType()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return \false;
 }
 public function supportsViews()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsViews() is deprecated. All platforms must implement support for views.');
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
 public final function modifyLimitQuery($query, $limit, $offset = 0) : string
 {
 if ($offset < 0) {
 throw new Exception(sprintf('Offset must be a positive integer or zero, %d given', $offset));
 }
 if ($offset > 0 && !$this->supportsLimitOffset()) {
 throw new Exception(sprintf('Platform %s does not support offset values in limit queries.', $this->getName()));
 }
 if ($limit !== null) {
 $limit = (int) $limit;
 }
 return $this->doModifyLimitQuery($query, $limit, (int) $offset);
 }
 protected function doModifyLimitQuery($query, $limit, $offset)
 {
 if ($limit !== null) {
 $query .= sprintf(' LIMIT %d', $limit);
 }
 if ($offset > 0) {
 $query .= sprintf(' OFFSET %d', $offset);
 }
 return $query;
 }
 public function supportsLimitOffset()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4724', 'AbstractPlatform::supportsViews() is deprecated.' . ' All platforms must implement support for offsets in modify limit clauses.');
 return \true;
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
 public final function getReservedKeywordsList() : KeywordList
 {
 // Store the instance so it doesn't need to be generated on every request.
 return $this->_keywords ??= $this->createReservedKeywordsList();
 }
 protected function createReservedKeywordsList() : KeywordList
 {
 $class = $this->getReservedKeywordsClass();
 $keywords = new $class();
 if (!$keywords instanceof KeywordList) {
 throw Exception::notSupported(__METHOD__);
 }
 return $keywords;
 }
 protected function getReservedKeywordsClass()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4510', 'AbstractPlatform::getReservedKeywordsClass() is deprecated,' . ' use AbstractPlatform::createReservedKeywordsList() instead.');
 throw Exception::notSupported(__METHOD__);
 }
 public function quoteStringLiteral($str)
 {
 $c = $this->getStringLiteralQuoteCharacter();
 return $c . str_replace($c, $c . $c, $str) . $c;
 }
 public function getStringLiteralQuoteCharacter()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5388', 'AbstractPlatform::getStringLiteralQuoteCharacter() is deprecated.' . ' Use quoteStringLiteral() instead.');
 return "'";
 }
 public final function escapeStringForLike(string $inputString, string $escapeChar) : string
 {
 return preg_replace('~([' . preg_quote($this->getLikeWildcardCharacters() . $escapeChar, '~') . '])~u', addcslashes($escapeChar, '\\') . '$1', $inputString);
 }
 private function columnToArray(Column $column) : array
 {
 $name = $column->getQuotedName($this);
 return array_merge($column->toArray(), ['name' => $name, 'version' => $column->hasPlatformOption('version') ? $column->getPlatformOption('version') : \false, 'comment' => $this->getColumnComment($column)]);
 }
 public function createSQLParser() : Parser
 {
 return new Parser(\false);
 }
 protected function getLikeWildcardCharacters() : string
 {
 return '%_';
 }
 public function columnsEqual(Column $column1, Column $column2) : bool
 {
 $column1Array = $this->columnToArray($column1);
 $column2Array = $this->columnToArray($column2);
 // ignore explicit columnDefinition since it's not set on the Column generated by the SchemaManager
 unset($column1Array['columnDefinition']);
 unset($column2Array['columnDefinition']);
 if ($this->getColumnDeclarationSQL('', $column1Array) !== $this->getColumnDeclarationSQL('', $column2Array)) {
 return \false;
 }
 if (!$this->columnDeclarationsMatch($column1, $column2)) {
 return \false;
 }
 // If the platform supports inline comments, all comparison is already done above
 if ($this->supportsInlineColumnComments()) {
 return \true;
 }
 if ($column1->getComment() !== $column2->getComment()) {
 return \false;
 }
 return $column1->getType() === $column2->getType();
 }
 private function columnDeclarationsMatch(Column $column1, Column $column2) : bool
 {
 return !($column1->hasPlatformOption('declarationMismatch') || $column2->hasPlatformOption('declarationMismatch'));
 }
 public function createSchemaManager(Connection $connection) : AbstractSchemaManager
 {
 throw Exception::notSupported(__METHOD__);
 }
}
