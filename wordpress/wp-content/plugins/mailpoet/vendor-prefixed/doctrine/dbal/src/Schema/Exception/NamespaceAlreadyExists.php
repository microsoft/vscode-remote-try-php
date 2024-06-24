<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\SchemaException;
use function sprintf;
final class NamespaceAlreadyExists extends SchemaException
{
 public static function new(string $namespaceName) : self
 {
 return new self(sprintf('The namespace with name "%s" already exists.', $namespaceName), self::NAMESPACE_ALREADY_EXISTS);
 }
}
