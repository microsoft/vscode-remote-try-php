<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
final class UnknownColumnOption extends SchemaException
{
 public static function new(string $name) : self
 {
 return new self(sprintf('The "%s" column option is not supported.', $name));
 }
}
