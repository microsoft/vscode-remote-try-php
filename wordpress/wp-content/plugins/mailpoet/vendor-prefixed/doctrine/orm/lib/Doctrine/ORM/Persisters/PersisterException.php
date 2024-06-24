<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use function sprintf;
class PersisterException extends ORMException
{
 public static function matchingAssocationFieldRequiresObject($class, $associationName)
 {
 return new self(sprintf('Cannot match on %s::%s with a non-object value. Matching objects by id is ' . 'not compatible with matching on an in-memory collection, which compares objects by reference.', $class, $associationName));
 }
}
