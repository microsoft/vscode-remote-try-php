<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\DB2Platform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class BooleanType extends Type
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getBooleanTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 return $platform->convertBooleansToDatabaseValue($value);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 return $platform->convertFromBoolean($value);
 }
 public function getName()
 {
 return Types::BOOLEAN;
 }
 public function getBindingType()
 {
 return ParameterType::BOOLEAN;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 // We require a commented boolean type in order to distinguish between
 // boolean and smallint as both (have to) map to the same native type.
 return $platform instanceof DB2Platform;
 }
}
