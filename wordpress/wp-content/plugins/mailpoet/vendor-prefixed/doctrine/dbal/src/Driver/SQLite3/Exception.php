<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\SQLite3;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractException;
final class Exception extends AbstractException
{
 public static function new(\Exception $exception) : self
 {
 return new self($exception->getMessage(), null, (int) $exception->getCode(), $exception);
 }
}
