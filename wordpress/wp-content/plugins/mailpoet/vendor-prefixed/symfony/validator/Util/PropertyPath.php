<?php
namespace MailPoetVendor\Symfony\Component\Validator\Util;
if (!defined('ABSPATH')) exit;
class PropertyPath
{
 public static function append(string $basePath, string $subPath)
 {
 if ('' !== $subPath) {
 if ('[' === $subPath[0]) {
 return $basePath . $subPath;
 }
 return '' !== $basePath ? $basePath . '.' . $subPath : $subPath;
 }
 return $basePath;
 }
 private function __construct()
 {
 }
}
