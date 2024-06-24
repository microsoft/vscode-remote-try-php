<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
trait MagicParameter
{
 private function getMagicParameter(array $parameters, int $index, string $key, $default)
 {
 if (\array_key_exists($index, $parameters)) {
 return $parameters[$index];
 }
 if (\array_key_exists($key, $parameters)) {
 return $parameters[$key];
 }
 return $default;
 }
}
