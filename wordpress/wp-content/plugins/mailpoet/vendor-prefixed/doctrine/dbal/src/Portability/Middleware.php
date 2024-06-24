<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Portability;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ColumnCase;
use MailPoetVendor\Doctrine\DBAL\Driver as DriverInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware as MiddlewareInterface;
final class Middleware implements MiddlewareInterface
{
 private int $mode;
 private int $case;
 public function __construct(int $mode, int $case)
 {
 $this->mode = $mode;
 $this->case = $case;
 }
 public function wrap(DriverInterface $driver) : DriverInterface
 {
 if ($this->mode !== 0) {
 return new Driver($driver, $this->mode, $this->case);
 }
 return $driver;
 }
}
