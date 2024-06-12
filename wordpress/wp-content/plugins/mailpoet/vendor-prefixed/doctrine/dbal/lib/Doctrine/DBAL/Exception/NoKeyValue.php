<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use function sprintf;
final class NoKeyValue extends Exception
{
 public static function fromColumnCount(int $columnCount) : self
 {
 return new self(sprintf('Fetching as key-value pairs requires the result to contain at least 2 columns, %d given.', $columnCount));
 }
}
