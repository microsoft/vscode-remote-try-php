<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_map;
use function get_class;
abstract class Type
{
 private const BUILTIN_TYPES_MAP = [Types::ARRAY => ArrayType::class, Types::ASCII_STRING => AsciiStringType::class, Types::BIGINT => BigIntType::class, Types::BINARY => BinaryType::class, Types::BLOB => BlobType::class, Types::BOOLEAN => BooleanType::class, Types::DATE_MUTABLE => DateType::class, Types::DATE_IMMUTABLE => DateImmutableType::class, Types::DATEINTERVAL => DateIntervalType::class, Types::DATETIME_MUTABLE => DateTimeType::class, Types::DATETIME_IMMUTABLE => DateTimeImmutableType::class, Types::DATETIMETZ_MUTABLE => DateTimeTzType::class, Types::DATETIMETZ_IMMUTABLE => DateTimeTzImmutableType::class, Types::DECIMAL => DecimalType::class, Types::FLOAT => FloatType::class, Types::GUID => GuidType::class, Types::INTEGER => IntegerType::class, Types::JSON => JsonType::class, Types::OBJECT => ObjectType::class, Types::SIMPLE_ARRAY => SimpleArrayType::class, Types::SMALLINT => SmallIntType::class, Types::STRING => StringType::class, Types::TEXT => TextType::class, Types::TIME_MUTABLE => TimeType::class, Types::TIME_IMMUTABLE => TimeImmutableType::class];
 private static ?TypeRegistry $typeRegistry = null;
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
 public abstract function getSQLDeclaration(array $column, AbstractPlatform $platform);
 public abstract function getName();
 public static final function getTypeRegistry() : TypeRegistry
 {
 return self::$typeRegistry ??= self::createTypeRegistry();
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
 public static function lookupName(self $type) : string
 {
 return self::getTypeRegistry()->lookupName($type);
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return \false;
 }
}
