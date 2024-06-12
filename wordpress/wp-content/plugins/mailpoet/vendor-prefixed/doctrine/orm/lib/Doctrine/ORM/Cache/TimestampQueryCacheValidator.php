<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use function microtime;
class TimestampQueryCacheValidator implements QueryCacheValidator
{
 private $timestampRegion;
 public function __construct(TimestampRegion $timestampRegion)
 {
 $this->timestampRegion = $timestampRegion;
 }
 public function isValid(QueryCacheKey $key, QueryCacheEntry $entry)
 {
 if ($this->regionUpdated($key, $entry)) {
 return \false;
 }
 if ($key->lifetime === 0) {
 return \true;
 }
 return $entry->time + $key->lifetime > microtime(\true);
 }
 private function regionUpdated(QueryCacheKey $key, QueryCacheEntry $entry) : bool
 {
 if ($key->timestampKey === null) {
 return \false;
 }
 $timestamp = $this->timestampRegion->get($key->timestampKey);
 return $timestamp && $timestamp->time > $entry->time;
 }
}
