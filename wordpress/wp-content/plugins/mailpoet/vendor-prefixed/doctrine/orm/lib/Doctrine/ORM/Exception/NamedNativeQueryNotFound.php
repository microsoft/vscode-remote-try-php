<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
final class NamedNativeQueryNotFound extends ORMException implements ConfigurationException
{
 public static function fromName(string $name) : self
 {
 return new self(sprintf('Could not find a named native query by the name "%s"', $name));
 }
}
