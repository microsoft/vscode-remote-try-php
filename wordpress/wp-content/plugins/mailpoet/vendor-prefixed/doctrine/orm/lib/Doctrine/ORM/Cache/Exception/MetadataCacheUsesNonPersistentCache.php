<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use function get_debug_type;
final class MetadataCacheUsesNonPersistentCache extends CacheException
{
 public static function fromDriver(Cache $cache) : self
 {
 return new self('Metadata Cache uses a non-persistent cache driver, ' . get_debug_type($cache) . '.');
 }
}
