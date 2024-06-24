<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
final class MetadataCacheNotConfigured extends CacheException
{
 public static function create() : self
 {
 return new self('Class Metadata Cache is not configured.');
 }
}
