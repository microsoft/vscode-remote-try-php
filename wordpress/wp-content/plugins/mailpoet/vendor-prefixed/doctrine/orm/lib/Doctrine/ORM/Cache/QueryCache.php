<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
interface QueryCache
{
 public function clear();
 public function put(QueryCacheKey $key, ResultSetMapping $rsm, $result, array $hints = []);
 public function get(QueryCacheKey $key, ResultSetMapping $rsm, array $hints = []);
 public function getRegion();
}
