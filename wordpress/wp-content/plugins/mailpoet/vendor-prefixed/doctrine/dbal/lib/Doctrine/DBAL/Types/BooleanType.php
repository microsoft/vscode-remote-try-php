<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
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
}
