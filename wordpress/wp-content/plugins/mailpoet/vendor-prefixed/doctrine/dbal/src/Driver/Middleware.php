<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver;
interface Middleware
{
 public function wrap(Driver $driver) : Driver;
}
