<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function is_resource;
use function restore_error_handler;
use function serialize;
use function set_error_handler;
use function stream_get_contents;
use function unserialize;
class ObjectType extends Type
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getClobTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 return serialize($value);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return null;
 }
 $value = is_resource($value) ? stream_get_contents($value) : $value;
 set_error_handler(function (int $code, string $message) : bool {
 throw ConversionException::conversionFailedUnserialization($this->getName(), $message);
 });
 try {
 return unserialize($value);
 } finally {
 restore_error_handler();
 }
 }
 public function getName()
 {
 return Types::OBJECT;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return \true;
 }
}
