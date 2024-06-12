<?php
namespace MailPoetVendor\Doctrine\Common\Proxy\Exception;
if (!defined('ABSPATH')) exit;
use OutOfBoundsException as BaseOutOfBoundsException;
use function sprintf;
class OutOfBoundsException extends BaseOutOfBoundsException implements ProxyException
{
 public static function missingPrimaryKeyValue($className, $idField)
 {
 return new self(sprintf('Missing value for primary key %s on %s', $idField, $className));
 }
}
