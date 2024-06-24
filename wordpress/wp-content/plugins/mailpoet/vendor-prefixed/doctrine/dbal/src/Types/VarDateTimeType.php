<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTime;
use DateTimeInterface;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use Exception;
class VarDateTimeType extends DateTimeType
{
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTime) {
 return $value;
 }
 try {
 $dateTime = new DateTime($value);
 } catch (Exception $e) {
 throw ConversionException::conversionFailed($value, $this->getName(), $e);
 }
 return $dateTime;
 }
}
