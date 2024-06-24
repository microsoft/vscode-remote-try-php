<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
use function current;
use function is_array;
use function is_bool;
use function is_int;
class ParameterTypeInferer
{
 public static function inferType($value)
 {
 if (is_int($value)) {
 return Types::INTEGER;
 }
 if (is_bool($value)) {
 return Types::BOOLEAN;
 }
 if ($value instanceof DateTimeImmutable) {
 return Types::DATETIME_IMMUTABLE;
 }
 if ($value instanceof DateTimeInterface) {
 return Types::DATETIME_MUTABLE;
 }
 if ($value instanceof DateInterval) {
 return Types::DATEINTERVAL;
 }
 if ($value instanceof BackedEnum) {
 return is_int($value->value) ? Types::INTEGER : Types::STRING;
 }
 if (is_array($value)) {
 $firstValue = current($value);
 if ($firstValue instanceof BackedEnum) {
 $firstValue = $firstValue->value;
 }
 return is_int($firstValue) ? Connection::PARAM_INT_ARRAY : Connection::PARAM_STR_ARRAY;
 }
 return ParameterType::STRING;
 }
}
