<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTime;
use DateTimeInterface;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function date_create;
class DateTimeType extends Type implements PhpDateTimeMappingType
{
 public function getName()
 {
 return Types::DATETIME_MUTABLE;
 }
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getDateTimeTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeInterface) {
 return $value->format($platform->getDateTimeFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeInterface) {
 return $value;
 }
 $val = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);
 if (!$val) {
 $val = date_create($value);
 }
 if (!$val) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
 }
 return $val;
 }
}
