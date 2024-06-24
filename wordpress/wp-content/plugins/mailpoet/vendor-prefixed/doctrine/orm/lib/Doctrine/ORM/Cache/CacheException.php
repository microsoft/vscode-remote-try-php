<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use function sprintf;
class CacheException extends ORMException
{
 public static function updateReadOnlyCollection($sourceEntity, $fieldName)
 {
 return new self(sprintf('Cannot update a readonly collection "%s#%s"', $sourceEntity, $fieldName));
 }
 public static function updateReadOnlyEntity($entityName)
 {
 return new self(sprintf('Cannot update a readonly entity "%s"', $entityName));
 }
 public static function nonCacheableEntity($entityName)
 {
 return new self(sprintf('Entity "%s" not configured as part of the second-level cache.', $entityName));
 }
 public static function nonCacheableEntityAssociation($entityName, $field)
 {
 return new self(sprintf('Entity association field "%s#%s" not configured as part of the second-level cache.', $entityName, $field));
 }
}
