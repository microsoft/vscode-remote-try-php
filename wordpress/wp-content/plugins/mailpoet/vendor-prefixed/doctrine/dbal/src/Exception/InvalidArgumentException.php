<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
class InvalidArgumentException extends Exception
{
 public static function fromEmptyCriteria()
 {
 return new self('Empty criteria was used, expected non-empty criteria');
 }
}
