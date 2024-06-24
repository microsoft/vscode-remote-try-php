<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver as DriverInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware as MiddlewareInterface;
use MailPoetVendor\Psr\Log\LoggerInterface;
final class Middleware implements MiddlewareInterface
{
 private LoggerInterface $logger;
 public function __construct(LoggerInterface $logger)
 {
 $this->logger = $logger;
 }
 public function wrap(DriverInterface $driver) : DriverInterface
 {
 return new Driver($driver, $this->logger);
 }
}
