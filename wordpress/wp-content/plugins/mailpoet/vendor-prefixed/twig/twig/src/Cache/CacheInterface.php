<?php
namespace MailPoetVendor\Twig\Cache;
if (!defined('ABSPATH')) exit;
interface CacheInterface
{
 public function generateKey(string $name, string $className) : string;
 public function write(string $key, string $content) : void;
 public function load(string $key) : void;
 public function getTimestamp(string $key) : int;
}
