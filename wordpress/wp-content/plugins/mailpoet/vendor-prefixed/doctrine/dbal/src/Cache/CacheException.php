<?php
namespace MailPoetVendor\Doctrine\DBAL\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
class CacheException extends Exception
{
 public static function noCacheKey()
 {
 return new self('No cache key was set.');
 }
 public static function noResultDriverConfigured()
 {
 return new self('Trying to cache a query but no result driver is configured.');
 }
}
