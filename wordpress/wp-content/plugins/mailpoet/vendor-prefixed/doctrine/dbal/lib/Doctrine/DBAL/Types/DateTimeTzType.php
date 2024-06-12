<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTime;
use DateTimeInterface;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class DateTimeTzType extends Type implements PhpDateTimeMappingType
{
 public function getName()
 {
 return Types::DATETIMETZ_MUTABLE;
 }
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getDateTimeTzTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeInterface) {
 return $value->format($platform->getDateTimeTzFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'DateTime']);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeInterface) {
 return $value;
 }
 $val = DateTime::createFromFormat($platform->getDateTimeTzFormatString(), $value);
 if (!$val) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
 }
 return $val;
 }
}
