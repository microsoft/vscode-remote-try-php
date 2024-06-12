<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function is_resource;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function stream_get_contents;
use const JSON_ERROR_NONE;
class JsonType extends Type
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getJsonTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return null;
 }
 $encoded = json_encode($value);
 if (json_last_error() !== JSON_ERROR_NONE) {
 throw ConversionException::conversionFailedSerialization($value, 'json', json_last_error_msg());
 }
 return $encoded;
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value === '') {
 return null;
 }
 if (is_resource($value)) {
 $value = stream_get_contents($value);
 }
 $val = json_decode($value, \true);
 if (json_last_error() !== JSON_ERROR_NONE) {
 throw ConversionException::conversionFailed($value, $this->getName());
 }
 return $val;
 }
 public function getName()
 {
 return Types::JSON;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return !$platform->hasNativeJsonType();
 }
}
