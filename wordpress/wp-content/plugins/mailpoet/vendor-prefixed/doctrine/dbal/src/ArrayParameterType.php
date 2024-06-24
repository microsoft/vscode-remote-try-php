<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
final class ArrayParameterType
{
 public const INTEGER = ParameterType::INTEGER + Connection::ARRAY_PARAM_OFFSET;
 public const STRING = ParameterType::STRING + Connection::ARRAY_PARAM_OFFSET;
 public const ASCII = ParameterType::ASCII + Connection::ARRAY_PARAM_OFFSET;
 public const BINARY = ParameterType::BINARY + Connection::ARRAY_PARAM_OFFSET;
 public static function toElementParameterType(int $type) : int
 {
 return $type - Connection::ARRAY_PARAM_OFFSET;
 }
 private function __construct()
 {
 }
}
