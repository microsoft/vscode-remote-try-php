<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTimeImmutable;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class DateImmutableType extends DateType
{
 public function getName()
 {
 return Types::DATE_IMMUTABLE;
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeImmutable) {
 return $value->format($platform->getDateFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTimeImmutable::class]);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeImmutable) {
 return $value;
 }
 $dateTime = DateTimeImmutable::createFromFormat('!' . $platform->getDateFormatString(), $value);
 if (!$dateTime) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
 }
 return $dateTime;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return \true;
 }
}
