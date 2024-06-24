<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function assert;
use function fopen;
use function fseek;
use function fwrite;
use function is_resource;
use function is_string;
class BinaryType extends Type
{
 public function getSQLDeclaration(array $column, AbstractPlatform $platform)
 {
 return $platform->getBinaryTypeDeclarationSQL($column);
 }
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null) {
 return null;
 }
 if (is_string($value)) {
 $fp = fopen('php://temp', 'rb+');
 assert(is_resource($fp));
 fwrite($fp, $value);
 fseek($fp, 0);
 $value = $fp;
 }
 if (!is_resource($value)) {
 throw ConversionException::conversionFailed($value, Types::BINARY);
 }
 return $value;
 }
 public function getName()
 {
 return Types::BINARY;
 }
 public function getBindingType()
 {
 return ParameterType::BINARY;
 }
}
