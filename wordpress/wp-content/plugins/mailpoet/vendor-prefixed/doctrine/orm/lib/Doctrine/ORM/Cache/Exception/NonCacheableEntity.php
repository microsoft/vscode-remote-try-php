<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
class NonCacheableEntity extends CacheException
{
 public static function fromEntity(string $entityName) : self
 {
 return new self(sprintf('Entity "%s" not configured as part of the second-level cache.', $entityName));
 }
}
