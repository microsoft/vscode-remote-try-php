<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
final class QueryCacheNotConfigured extends CacheException
{
 public static function create() : self
 {
 return new self('Query Cache is not configured.');
 }
}
