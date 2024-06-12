<?php
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class LoggerChain implements SQLLogger
{
 private $loggers = [];
 public function __construct(array $loggers = [])
 {
 $this->loggers = $loggers;
 }
 public function addLogger(SQLLogger $logger)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3572', 'LoggerChain::addLogger() is deprecated, use LoggerChain constructor instead.');
 $this->loggers[] = $logger;
 }
 public function startQuery($sql, ?array $params = null, ?array $types = null)
 {
 foreach ($this->loggers as $logger) {
 $logger->startQuery($sql, $params, $types);
 }
 }
 public function stopQuery()
 {
 foreach ($this->loggers as $logger) {
 $logger->stopQuery();
 }
 }
}
