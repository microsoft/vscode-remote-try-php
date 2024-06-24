<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
final class AsciiStringType extends StringType
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform) : string
 {
 return $platform->getAsciiStringTypeDeclarationSQL($column);
 }
 public function getBindingType() : int
 {
 return ParameterType::ASCII;
 }
 public function getName() : string
 {
 return Types::ASCII_STRING;
 }
}
