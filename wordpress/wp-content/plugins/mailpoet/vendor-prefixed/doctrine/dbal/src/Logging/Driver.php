<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver as DriverInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use MailPoetVendor\Psr\Log\LoggerInterface;
use MailPoetVendor\SensitiveParameter;
final class Driver extends AbstractDriverMiddleware
{
 private LoggerInterface $logger;
 public function __construct(DriverInterface $driver, LoggerInterface $logger)
 {
 parent::__construct($driver);
 $this->logger = $logger;
 }
 public function connect( array $params)
 {
 $this->logger->info('Connecting with parameters {params}', ['params' => $this->maskPassword($params)]);
 return new Connection(parent::connect($params), $this->logger);
 }
 private function maskPassword( array $params) : array
 {
 if (isset($params['password'])) {
 $params['password'] = '<redacted>';
 }
 if (isset($params['url'])) {
 $params['url'] = '<redacted>';
 }
 return $params;
 }
}
