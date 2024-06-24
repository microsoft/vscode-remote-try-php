<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use function sprintf;
class DatabaseRequired extends Exception
{
 public static function new(string $methodName) : self
 {
 return new self(sprintf('A database is required for the method: %s.', $methodName));
 }
}
