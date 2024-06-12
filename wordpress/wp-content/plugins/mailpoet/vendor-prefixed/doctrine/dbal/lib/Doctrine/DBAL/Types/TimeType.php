<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTime;
use DateTimeInterface;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class TimeType extends Type
{
 public function getName()
 {
 return Types::TIME_MUTABLE;
 }
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getTimeTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeInterface) {
 return $value->format($platform->getTimeFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeInterface) {
 return $value;
 }
 $val = DateTime::createFromFormat('!' . $platform->getTimeFormatString(), $value);
 if (!$val) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getTimeFormatString());
 }
 return $val;
 }
}
