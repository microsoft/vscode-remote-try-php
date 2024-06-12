<?php
namespace MailPoetVendor\Psr\Cache;
if (!defined('ABSPATH')) exit;
interface CacheItemInterface
{
 public function getKey();
 public function get();
 public function isHit();
 public function set($value);
 public function expiresAt($expiration);
 public function expiresAfter($time);
}
