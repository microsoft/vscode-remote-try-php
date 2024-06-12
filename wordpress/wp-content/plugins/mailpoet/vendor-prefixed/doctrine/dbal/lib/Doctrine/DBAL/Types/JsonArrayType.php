<?php
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function is_resource;
use function json_decode;
use function stream_get_contents;
class JsonArrayType extends JsonType
{
 public function convertToPHPValue($value, AbstractPlatform $platform)
 {
 if ($value === null || $value === '') {
 return [];
 }
 $value = is_resource($value) ? stream_get_contents($value) : $value;
 return json_decode($value, \true);
 }
 public function getName()
 {
 return Types::JSON_ARRAY;
 }
 public function requiresSQLCommentHint(AbstractPlatform $platform)
 {
 return \true;
 }
}
