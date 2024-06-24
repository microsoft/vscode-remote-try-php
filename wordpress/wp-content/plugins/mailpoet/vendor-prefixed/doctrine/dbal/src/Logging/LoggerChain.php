<?php
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class LoggerChain implements SQLLogger
{
 private iterable $loggers;
 public function __construct(iterable $loggers = [])
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4967', 'LoggerChain is deprecated');
 $this->loggers = $loggers;
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
