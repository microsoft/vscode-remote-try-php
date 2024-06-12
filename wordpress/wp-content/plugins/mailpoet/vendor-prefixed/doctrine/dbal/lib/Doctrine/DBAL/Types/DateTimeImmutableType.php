<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTimeImmutable;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function date_create_immutable;
class DateTimeImmutableType extends DateTimeType
{
 public function getName()
 {
 return Types::DATETIME_IMMUTABLE;
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeImmutable) {
 return $value->format($platform->getDateTimeFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTimeImmutable::class]);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeImmutable) {
 return $value;
 }
 $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeFormatString(), $value);
 if (!$dateTime) {
 $dateTime = date_create_immutable($value);
 }
 if (!$dateTime) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
 }
 return $dateTime;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return \true;
 }
}
