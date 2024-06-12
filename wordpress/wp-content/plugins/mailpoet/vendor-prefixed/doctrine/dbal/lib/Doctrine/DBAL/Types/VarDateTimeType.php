<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use DateTime;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function date_create;
class VarDateTimeType extends DateTimeType
{
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value instanceof DateTime) {
 return $value;
 }
 $val = date_create($value);
 if (!$val) {
 throw ConversionException::conversionFailed($value, $this->getName());
 }
 return $val;
 }
}
