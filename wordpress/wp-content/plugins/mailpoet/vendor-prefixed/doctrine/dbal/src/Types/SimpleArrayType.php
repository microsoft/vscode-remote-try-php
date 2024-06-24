<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function count;
use function explode;
use function implode;
use function is_array;
use function is_resource;
use function stream_get_contents;
class SimpleArrayType extends Type
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getClobTypeDeclarationSQL($column);
 }
 public function convertToDatabaseValue($value, AbstractPlatform $platform)
 {
 if (!is_array($value) || count($value) === 0) {
 return null;
 }
 return implode(',', $value);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return [];
 }
 $value = is_resource($value) ? stream_get_contents($value) : $value;
 return explode(',', $value);
 }
 public function getName()
 {
 return Types::SIMPLE_ARRAY;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return \true;
 }
}
