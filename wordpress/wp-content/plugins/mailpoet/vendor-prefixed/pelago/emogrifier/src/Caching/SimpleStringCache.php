<?php
declare (strict_types=1);
namespace MailPoetVendor\Pelago\Emogrifier\Caching;
if (!defined('ABSPATH')) exit;
class SimpleStringCache
{
 private $values = [];
 public function has(string $key) : bool
 {
 $this->assertNotEmptyKey($key);
 return isset($this->values[$key]);
 }
 public function get(string $key) : string
 {
 if (!$this->has($key)) {
 throw new \BadMethodCallException('You can only call `get` with a key for an existing value.', 1625996246);
 }
 return $this->values[$key];
 }
 public function set(string $key, string $value) : void
 {
 $this->assertNotEmptyKey($key);
 $this->values[$key] = $value;
 }
 private function assertNotEmptyKey(string $key) : void
 {
 if ($key === '') {
 throw new \InvalidArgumentException('Please provide a non-empty key.', 1625995840);
 }
 }
}
