<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTimeImmutable;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class DateTimeTzImmutableType extends DateTimeTzType
{
 public function getName()
 {
 return Types::DATETIMETZ_IMMUTABLE;
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeImmutable) {
 return $value->format($platform->getDateTimeTzFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTimeImmutable::class]);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeImmutable) {
 return $value;
 }
 $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeTzFormatString(), $value);
 if (!$dateTime) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
 }
 return $dateTime;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return \true;
 }
}
