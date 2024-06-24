<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function is_resource;
use function stream_get_contents;
class TextType extends Type
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getClobTypeDeclarationSQL($column);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 return is_resource($value) ? stream_get_contents($value) : $value;
 }
 public function getName()
 {
 return Types::TEXT;
 }
}
