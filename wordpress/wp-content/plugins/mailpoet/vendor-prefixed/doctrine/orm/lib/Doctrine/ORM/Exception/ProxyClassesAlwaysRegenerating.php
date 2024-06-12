<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
final class ProxyClassesAlwaysRegenerating extends ORMException implements ConfigurationException
{
 public static function create() : self
 {
 return new self('Proxy Classes are always regenerating.');
 }
}
