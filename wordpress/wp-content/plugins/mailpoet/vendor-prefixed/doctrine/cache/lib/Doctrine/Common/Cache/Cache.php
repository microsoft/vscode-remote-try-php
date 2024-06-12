<?php
namespace MailPoetVendor\Doctrine\Common\Cache;
if (!defined('ABSPATH')) exit;
interface Cache
{
 public const STATS_HITS = 'hits';
 public const STATS_MISSES = 'misses';
 public const STATS_UPTIME = 'uptime';
 public const STATS_MEMORY_USAGE = 'memory_usage';
 public const STATS_MEMORY_AVAILABLE = 'memory_available';
 public const STATS_MEMORY_AVAILIABLE = 'memory_available';
 public function fetch($id);
 public function contains($id);
 public function save($id, $data, $lifeTime = 0);
 public function delete($id);
 public function getStats();
}
