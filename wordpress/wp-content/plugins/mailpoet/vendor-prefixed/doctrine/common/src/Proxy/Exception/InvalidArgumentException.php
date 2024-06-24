<?php
namespace MailPoetVendor\Doctrine\Common\Proxy\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Proxy;
use InvalidArgumentException as BaseInvalidArgumentException;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;
class InvalidArgumentException extends BaseInvalidArgumentException implements ProxyException
{
 public static function proxyDirectoryRequired()
 {
 return new self('You must configure a proxy directory. See docs for details');
 }
 public static function notProxyClass($className, $proxyNamespace)
 {
 return new self(sprintf('The class "%s" is not part of the proxy namespace "%s"', $className, $proxyNamespace));
 }
 public static function invalidPlaceholder($name)
 {
 return new self(sprintf('Provided placeholder for "%s" must be either a string or a valid callable', $name));
 }
 public static function proxyNamespaceRequired()
 {
 return new self('You must configure a proxy namespace');
 }
 public static function unitializedProxyExpected(Proxy $proxy)
 {
 return new self(sprintf('Provided proxy of type "%s" must not be initialized.', get_class($proxy)));
 }
 public static function invalidClassNotFoundCallback($callback)
 {
 $type = is_object($callback) ? get_class($callback) : gettype($callback);
 return new self(sprintf('Invalid \\$notFoundCallback given: must be a callable, "%s" given', $type));
 }
 public static function classMustNotBeAbstract($className)
 {
 return new self(sprintf('Unable to create a proxy for an abstract class "%s".', $className));
 }
 public static function classMustNotBeFinal($className)
 {
 return new self(sprintf('Unable to create a proxy for a final class "%s".', $className));
 }
 public static function classMustNotBeReadOnly($className)
 {
 return new self(sprintf('Unable to create a proxy for a readonly class "%s".', $className));
 }
 public static function invalidAutoGenerateMode($value) : self
 {
 return new self(sprintf('Invalid auto generate mode "%s" given.', $value));
 }
}
