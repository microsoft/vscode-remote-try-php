<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\QueryCacheKey;
class CacheLoggerChain implements CacheLogger
{
 private $loggers = [];
 public function setLogger($name, CacheLogger $logger)
 {
 $this->loggers[$name] = $logger;
 }
 public function getLogger($name)
 {
 return $this->loggers[$name] ?? null;
 }
 public function getLoggers()
 {
 return $this->loggers;
 }
 public function collectionCacheHit($regionName, CollectionCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->collectionCacheHit($regionName, $key);
 }
 }
 public function collectionCacheMiss($regionName, CollectionCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->collectionCacheMiss($regionName, $key);
 }
 }
 public function collectionCachePut($regionName, CollectionCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->collectionCachePut($regionName, $key);
 }
 }
 public function entityCacheHit($regionName, EntityCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->entityCacheHit($regionName, $key);
 }
 }
 public function entityCacheMiss($regionName, EntityCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->entityCacheMiss($regionName, $key);
 }
 }
 public function entityCachePut($regionName, EntityCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->entityCachePut($regionName, $key);
 }
 }
 public function queryCacheHit($regionName, QueryCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->queryCacheHit($regionName, $key);
 }
 }
 public function queryCacheMiss($regionName, QueryCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->queryCacheMiss($regionName, $key);
 }
 }
 public function queryCachePut($regionName, QueryCacheKey $key)
 {
 foreach ($this->loggers as $logger) {
 $logger->queryCachePut($regionName, $key);
 }
 }
}
