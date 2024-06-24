<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
final class UnknownEntityNamespace extends ORMException implements ConfigurationException
{
 public static function fromNamespaceAlias(string $entityNamespaceAlias) : self
 {
 return new self(sprintf('Unknown Entity namespace alias "%s"', $entityNamespaceAlias));
 }
}
