<?php
namespace MailPoetVendor\Twig\Cache;
if (!defined('ABSPATH')) exit;
final class NullCache implements CacheInterface
{
 public function generateKey(string $name, string $className) : string
 {
 return '';
 }
 public function write(string $key, string $content) : void
 {
 }
 public function load(string $key) : void
 {
 }
 public function getTimestamp(string $key) : int
 {
 return 0;
 }
}
