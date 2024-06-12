<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function explode;
use function implode;
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
 if (!$value) {
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
 return \true;
 }
}
