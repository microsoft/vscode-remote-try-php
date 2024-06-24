<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Query;
interface ExceptionConverter
{
 public function convert(Exception $exception, ?Query $query) : DriverException;
}
