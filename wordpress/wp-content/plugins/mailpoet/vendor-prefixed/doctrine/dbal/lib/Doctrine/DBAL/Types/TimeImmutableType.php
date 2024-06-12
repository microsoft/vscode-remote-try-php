<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTimeImmutable;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class TimeImmutableType extends TimeType
{
 public function getName()
 {
 return Types::TIME_IMMUTABLE;
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return $value;
 }
 if ($value instanceof DateTimeImmutable) {
 return $value->format($platform->getTimeFormatString());
 }
 throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTimeImmutable::class]);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTimeImmutable) {
 return $value;
 }
 $dateTime = DateTimeImmutable::createFromFormat('!' . $platform->getTimeFormatString(), $value);
 if (!$dateTime) {
 throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getTimeFormatString());
 }
 return $dateTime;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return \true;
 }
}
