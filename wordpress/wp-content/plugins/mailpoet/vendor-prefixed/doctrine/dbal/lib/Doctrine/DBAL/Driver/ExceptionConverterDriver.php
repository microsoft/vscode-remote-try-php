<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\DriverException as TheDriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
interface ExceptionConverterDriver
{
 public function convertException($message, TheDriverException $exception);
}
