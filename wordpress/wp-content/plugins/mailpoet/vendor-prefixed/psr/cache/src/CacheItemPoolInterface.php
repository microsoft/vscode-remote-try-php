<?php
namespace MailPoetVendor\Psr\Cache;
if (!defined('ABSPATH')) exit;
interface CacheItemPoolInterface
{
 public function getItem($key);
 public function getItems(array $keys = array());
 public function hasItem($key);
 public function clear();
 public function deleteItem($key);
 public function deleteItems(array $keys);
 public function save(CacheItemInterface $item);
 public function saveDeferred(CacheItemInterface $item);
 public function commit();
}
