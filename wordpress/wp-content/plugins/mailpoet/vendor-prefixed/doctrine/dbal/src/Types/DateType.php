<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function get_class;
class DateType extends Type
{
 public function getName()
 {
 return Types::DATE_MUTABLE;
 }
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getDateTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeInterface) {
 if ($value instanceof DateTimeImmutable) {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6017', 'Passing an instance of %s is deprecated, use %s::%s() instead.', get_class($value), DateImmutableType::class, __FUNCTION__);
 }
 return $value->format($platform->getDateFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTime::class]);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value instanceof DateTimeImmutable) {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6017', 'Passing an instance of %s is deprecated, use %s::%s() instead.', get_class($value), DateImmutableType::class, __FUNCTION__);
 }
 if ($value === null || $value instanceof DateTimeInterface) {
 return $value;
 }
 $dateTime = DateTime::createFromFormat('!' . $platform->getDateFormatString(), $value);
 if ($dateTime !== \false) {
 return $dateTime;
 }
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
 }
}
