<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
class TimestampCacheKey extends CacheKey
{
 public function __construct($space)
 {
 $this->hash = (string) $space;
 }
}
