<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use JsonException;
use function is_resource;
use function json_decode;
use function json_encode;
use function stream_get_contents;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
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
 try {
 return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
 } catch (JsonException $e) {
 throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
 }
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value === '') {
 return null;
 }
 if (is_resource($value)) {
 $value = stream_get_contents($value);
 }
 try {
 return json_decode($value, \true, 512, JSON_THROW_ON_ERROR);
 } catch (JsonException $e) {
 throw ConversionException::conversionFailed($value, $this->getName(), $e);
 }
 }
 public function getName()
 {
 return Types::JSON;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return !$platform->hasNativeJsonType();
 }
}
