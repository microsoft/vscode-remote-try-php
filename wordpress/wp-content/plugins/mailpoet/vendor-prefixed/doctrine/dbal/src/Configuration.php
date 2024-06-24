<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware;
use MailPoetVendor\Doctrine\DBAL\Logging\SQLLogger;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaManagerFactory;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use function func_num_args;
class Configuration
{
 private array $middlewares = [];
 protected $sqlLogger;
 private ?CacheItemPoolInterface $resultCache = null;
 protected $resultCacheImpl;
 protected $schemaAssetsFilter;
 protected $autoCommit = \true;
 private bool $disableTypeComments = \false;
 private ?SchemaManagerFactory $schemaManagerFactory = null;
 public function __construct()
 {
 $this->schemaAssetsFilter = static function () : bool {
 return \true;
 };
 }
 public function setSQLLogger(?SQLLogger $logger = null) : void
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4967', '%s is deprecated, use setMiddlewares() and Logging\\Middleware instead.', __METHOD__);
 $this->sqlLogger = $logger;
 }
 public function getSQLLogger() : ?SQLLogger
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4967', '%s is deprecated.', __METHOD__);
 return $this->sqlLogger;
 }
 public function getResultCache() : ?CacheItemPoolInterface
 {
 return $this->resultCache;
 }
 public function getResultCacheImpl() : ?Cache
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4620', '%s is deprecated, call getResultCache() instead.', __METHOD__);
 return $this->resultCacheImpl;
 }
 public function setResultCache(CacheItemPoolInterface $cache) : void
 {
 $this->resultCacheImpl = DoctrineProvider::wrap($cache);
 $this->resultCache = $cache;
 }
 public function setResultCacheImpl(Cache $cacheImpl) : void
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4620', '%s is deprecated, call setResultCache() instead.', __METHOD__);
 $this->resultCacheImpl = $cacheImpl;
 $this->resultCache = CacheAdapter::wrap($cacheImpl);
 }
 public function setSchemaAssetsFilter(?callable $callable = null) : void
 {
 if (func_num_args() < 1) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5483', 'Not passing an argument to %s is deprecated.', __METHOD__);
 } elseif ($callable === null) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5483', 'Using NULL as a schema asset filter is deprecated.' . ' Use a callable that always returns true instead.');
 }
 $this->schemaAssetsFilter = $callable;
 }
 public function getSchemaAssetsFilter() : ?callable
 {
 return $this->schemaAssetsFilter;
 }
 public function setAutoCommit(bool $autoCommit) : void
 {
 $this->autoCommit = $autoCommit;
 }
 public function getAutoCommit() : bool
 {
 return $this->autoCommit;
 }
 public function setMiddlewares(array $middlewares) : self
 {
 $this->middlewares = $middlewares;
 return $this;
 }
 public function getMiddlewares() : array
 {
 return $this->middlewares;
 }
 public function getSchemaManagerFactory() : ?SchemaManagerFactory
 {
 return $this->schemaManagerFactory;
 }
 public function setSchemaManagerFactory(SchemaManagerFactory $schemaManagerFactory) : self
 {
 $this->schemaManagerFactory = $schemaManagerFactory;
 return $this;
 }
 public function getDisableTypeComments() : bool
 {
 return $this->disableTypeComments;
 }
 public function setDisableTypeComments(bool $disableTypeComments) : self
 {
 $this->disableTypeComments = $disableTypeComments;
 return $this;
 }
}
