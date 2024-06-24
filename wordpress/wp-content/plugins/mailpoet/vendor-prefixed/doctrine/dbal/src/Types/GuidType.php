<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
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
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return !$platform->hasNativeGuidType();
 }
}
