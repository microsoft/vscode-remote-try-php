<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
final class InvalidResultCacheDriver extends CacheException
{
 public static function create() : self
 {
 return new self('Invalid result cache driver; it must implement Doctrine\\Common\\Cache\\Cache.');
 }
}
