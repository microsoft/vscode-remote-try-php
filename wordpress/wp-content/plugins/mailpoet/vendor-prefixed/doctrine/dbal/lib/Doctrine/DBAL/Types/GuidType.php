<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
class GuidType extends StringType
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getGuidTypeDeclarationSQL($column);
 }
 public function getName()
 {
 return Types::GUID;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return !$platform->hasNativeGuidType();
 }
}
