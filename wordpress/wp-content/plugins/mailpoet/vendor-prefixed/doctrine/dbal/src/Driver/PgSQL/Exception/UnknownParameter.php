<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PgSQL\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractException;
use function sprintf;
final class UnknownParameter extends AbstractException
{
 public static function new(string $param) : self
 {
 return new self(sprintf('Could not find parameter %s in the SQL statement', $param));
 }
}
