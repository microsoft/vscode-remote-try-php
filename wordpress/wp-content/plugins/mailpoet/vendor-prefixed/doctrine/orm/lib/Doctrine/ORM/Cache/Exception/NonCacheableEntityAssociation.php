<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
class NonCacheableEntityAssociation extends CacheException
{
 public static function fromEntityAndField(string $entityName, string $field) : self
 {
 return new self(sprintf('Entity association field "%s#%s" not configured as part of the second-level cache.', $entityName, $field));
 }
}
