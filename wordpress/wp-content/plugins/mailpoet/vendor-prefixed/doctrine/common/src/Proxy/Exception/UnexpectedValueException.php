<?php
namespace MailPoetVendor\Doctrine\Common\Proxy\Exception;
if (!defined('ABSPATH')) exit;
use Throwable;
use UnexpectedValueException as BaseUnexpectedValueException;
use function sprintf;
class UnexpectedValueException extends BaseUnexpectedValueException implements ProxyException
{
 public static function proxyDirectoryNotWritable($proxyDirectory)
 {
 return new self(sprintf('Your proxy directory "%s" must be writable', $proxyDirectory));
 }
 public static function invalidParameterTypeHint($className, $methodName, $parameterName, ?Throwable $previous = null)
 {
 return new self(sprintf('The type hint of parameter "%s" in method "%s" in class "%s" is invalid.', $parameterName, $methodName, $className), 0, $previous);
 }
 public static function invalidReturnTypeHint($className, $methodName, ?Throwable $previous = null)
 {
 return new self(sprintf('The return type of method "%s" in class "%s" is invalid.', $methodName, $className), 0, $previous);
 }
}
