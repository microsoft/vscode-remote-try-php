<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\Logging\CacheLogger;
class CacheConfiguration
{
 private $cacheFactory;
 private $regionsConfig;
 private $cacheLogger;
 private $queryValidator;
 public function getCacheFactory()
 {
 return $this->cacheFactory;
 }
 public function setCacheFactory(CacheFactory $factory)
 {
 $this->cacheFactory = $factory;
 }
 public function getCacheLogger()
 {
 return $this->cacheLogger;
 }
 public function setCacheLogger(CacheLogger $logger)
 {
 $this->cacheLogger = $logger;
 }
 public function getRegionsConfiguration()
 {
 if ($this->regionsConfig === null) {
 $this->regionsConfig = new RegionsConfiguration();
 }
 return $this->regionsConfig;
 }
 public function setRegionsConfiguration(RegionsConfiguration $regionsConfig)
 {
 $this->regionsConfig = $regionsConfig;
 }
 public function getQueryValidator()
 {
 if ($this->queryValidator === null) {
 $this->queryValidator = new TimestampQueryCacheValidator($this->cacheFactory->getTimestampRegion());
 }
 return $this->queryValidator;
 }
 public function setQueryValidator(QueryCacheValidator $validator)
 {
 $this->queryValidator = $validator;
 }
}
