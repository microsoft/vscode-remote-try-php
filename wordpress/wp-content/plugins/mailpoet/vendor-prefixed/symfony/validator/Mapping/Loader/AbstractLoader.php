<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
abstract class AbstractLoader implements LoaderInterface
{
 public const DEFAULT_NAMESPACE = '\\Symfony\\Component\\Validator\\Constraints\\';
 protected $namespaces = [];
 protected function addNamespaceAlias(string $alias, string $namespace)
 {
 $this->namespaces[$alias] = $namespace;
 }
 protected function newConstraint(string $name, $options = null)
 {
 if (\str_contains($name, '\\') && \class_exists($name)) {
 $className = $name;
 } elseif (\str_contains($name, ':')) {
 [$prefix, $className] = \explode(':', $name, 2);
 if (!isset($this->namespaces[$prefix])) {
 throw new MappingException(\sprintf('Undefined namespace prefix "%s".', $prefix));
 }
 $className = $this->namespaces[$prefix] . $className;
 } else {
 $className = self::DEFAULT_NAMESPACE . $name;
 }
 return new $className($options);
 }
}
