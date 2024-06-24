<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class FloatType extends Type
{
 public function getName()
 {
 return Types::FLOAT;
 }
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getFloatDeclarationSQL($column);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 return $value === null ? null : (float) $value;
 }
}
