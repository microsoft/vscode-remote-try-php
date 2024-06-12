<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\DBAL\Logging\SQLLogger;
use MailPoetVendor\Doctrine\DBAL\Schema\AbstractAsset;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function preg_match;
class Configuration
{
 protected $_attributes = [];
 public function setSQLLogger(?SQLLogger $logger = null)
 {
 $this->_attributes['sqlLogger'] = $logger;
 }
 public function getSQLLogger()
 {
 return $this->_attributes['sqlLogger'] ?? null;
 }
 public function getResultCacheImpl()
 {
 return $this->_attributes['resultCacheImpl'] ?? null;
 }
 public function setResultCacheImpl(Cache $cacheImpl)
 {
 $this->_attributes['resultCacheImpl'] = $cacheImpl;
 }
 public function setFilterSchemaAssetsExpression($filterExpression)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3316', 'Configuration::setFilterSchemaAssetsExpression() is deprecated, use setSchemaAssetsFilter() instead.');
 $this->_attributes['filterSchemaAssetsExpression'] = $filterExpression;
 if ($filterExpression) {
 $this->_attributes['filterSchemaAssetsExpressionCallable'] = $this->buildSchemaAssetsFilterFromExpression($filterExpression);
 } else {
 $this->_attributes['filterSchemaAssetsExpressionCallable'] = null;
 }
 }
 public function getFilterSchemaAssetsExpression()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3316', 'Configuration::getFilterSchemaAssetsExpression() is deprecated, use getSchemaAssetsFilter() instead.');
 return $this->_attributes['filterSchemaAssetsExpression'] ?? null;
 }
 private function buildSchemaAssetsFilterFromExpression($filterExpression) : callable
 {
 return static function ($assetName) use($filterExpression) {
 if ($assetName instanceof AbstractAsset) {
 $assetName = $assetName->getName();
 }
 return preg_match($filterExpression, $assetName);
 };
 }
 public function setSchemaAssetsFilter(?callable $callable = null) : ?callable
 {
 $this->_attributes['filterSchemaAssetsExpression'] = null;
 return $this->_attributes['filterSchemaAssetsExpressionCallable'] = $callable;
 }
 public function getSchemaAssetsFilter() : ?callable
 {
 return $this->_attributes['filterSchemaAssetsExpressionCallable'] ?? null;
 }
 public function setAutoCommit($autoCommit)
 {
 $this->_attributes['autoCommit'] = (bool) $autoCommit;
 }
 public function getAutoCommit()
 {
 return $this->_attributes['autoCommit'] ?? \true;
 }
}
