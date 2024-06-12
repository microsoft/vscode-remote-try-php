<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_map;
use function get_class;
use function str_replace;
use function strrpos;
use function substr;
abstract class Type
{
 public const BIGINT = Types::BIGINT;
 public const BINARY = Types::BINARY;
 public const BLOB = Types::BLOB;
 public const BOOLEAN = Types::BOOLEAN;
 public const DATE = Types::DATE_MUTABLE;
 public const DATE_IMMUTABLE = Types::DATE_IMMUTABLE;
 public const DATEINTERVAL = Types::DATEINTERVAL;
 public const DATETIME = Types::DATETIME_MUTABLE;
 public const DATETIME_IMMUTABLE = Types::DATETIME_IMMUTABLE;
 public const DATETIMETZ = Types::DATETIMETZ_MUTABLE;
 public const DATETIMETZ_IMMUTABLE = Types::DATETIMETZ_IMMUTABLE;
 public const DECIMAL = Types::DECIMAL;
 public const FLOAT = Types::FLOAT;
 public const GUID = Types::GUID;
 public const INTEGER = Types::INTEGER;
 public const JSON = Types::JSON;
 public const JSON_ARRAY = Types::JSON_ARRAY;
 public const OBJECT = Types::OBJECT;
 public const SIMPLE_ARRAY = Types::SIMPLE_ARRAY;
 public const SMALLINT = Types::SMALLINT;
 public const STRING = Types::STRING;
 public const TARRAY = Types::ARRAY;
 public const TEXT = Types::TEXT;
 public const TIME = Types::TIME_MUTABLE;
 public const TIME_IMMUTABLE = Types::TIME_IMMUTABLE;
 private const BUILTIN_TYPES_MAP = [Types::ARRAY => ArrayType::class, Types::ASCII_STRING => AsciiStringType::class, Types::BIGINT => BigIntType::class, Types::BINARY => BinaryType::class, Types::BLOB => BlobType::class, Types::BOOLEAN => BooleanType::class, Types::DATE_MUTABLE => DateType::class, Types::DATE_IMMUTABLE => DateImmutableType::class, Types::DATEINTERVAL => DateIntervalType::class, Types::DATETIME_MUTABLE => DateTimeType::class, Types::DATETIME_IMMUTABLE => DateTimeImmutableType::class, Types::DATETIMETZ_MUTABLE => DateTimeTzType::class, Types::DATETIMETZ_IMMUTABLE => DateTimeTzImmutableType::class, Types::DECIMAL => DecimalType::class, Types::FLOAT => FloatType::class, Types::GUID => GuidType::class, Types::INTEGER => IntegerType::class, Types::JSON => JsonType::class, Types::JSON_ARRAY => JsonArrayType::class, Types::OBJECT => ObjectType::class, Types::SIMPLE_ARRAY => SimpleArrayType::class, Types::SMALLINT => SmallIntType::class, Types::STRING => StringType::class, Types::TEXT => TextType::class, Types::TIME_MUTABLE => TimeType::class, Types::TIME_IMMUTABLE => TimeImmutableType::class];
 private static $typeRegistry;
 public final function __construct()
 {
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 return $value;
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 return $value;
 }
 public function getDefaultLength(AbstractPlatform $platform)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3255', 'Type::getDefaultLength() is deprecated, use AbstractPlatform directly.');
 return null;
 }
 public abstract function getSQLDeclaration(array $column, AbstractPlatform $platform);
 public abstract function getName();
 public static final function getTypeRegistry() : TypeRegistry
 {
 if (self::$typeRegistry === null) {
 self::$typeRegistry = self::createTypeRegistry();
 }
 return self::$typeRegistry;
 }
 private static function createTypeRegistry() : TypeRegistry
 {
 $instances = [];
 foreach (self::BUILTIN_TYPES_MAP as $name => $class) {
 $instances[$name] = new $class();
 }
 return new TypeRegistry($instances);
 }
 public static function getType($name)
 {
 return self::getTypeRegistry()->get($name);
 }
 public static function addType($name, $className)
 {
 self::getTypeRegistry()->register($name, new $className());
 }
 public static function hasType($name)
 {
 return self::getTypeRegistry()->has($name);
 }
 public static function overrideType($name, $className)
 {
 self::getTypeRegistry()->override($name, new $className());
 }
 public function getBindingType()
 {
 return ParameterType::STRING;
 }
 public static function getTypesMap()
 {
 return array_map(static function (Type $type) : string {
 return get_class($type);
 }, self::getTypeRegistry()->getMap());
 }
 public function __toString()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3258', 'Type::__toString() is deprecated, use Type::getName() or get_class($type) instead.');
 $type = static::class;
 $position = strrpos($type, '\\');
 if ($position !== \false) {
 $type = substr($type, $position);
 }
 return str_replace('Type', '', $type);
 }
 public function canRequireSQLConversion()
 {
 return \false;
 }
 public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
 {
 return $sqlExpr;
 }
 public function convertToPHPValueSQL($sqlExpr, $platform)
 {
 return $sqlExpr;
 }
 public function getMappedDatabaseTypes(AbstractPlatform $platform)
 {
 return [];
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return \false;
 }
}
