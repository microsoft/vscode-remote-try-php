<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
use function implode;
use function sprintf;
final class UnrecognizedIdentifierFields extends ORMException implements ManagerException
{
 public static function fromClassAndFieldNames(string $className, array $fieldNames) : self
 {
 return new self(sprintf('Unrecognized identifier fields: "%s" are not present on class "%s".', implode("', '", $fieldNames), $className));
 }
}
