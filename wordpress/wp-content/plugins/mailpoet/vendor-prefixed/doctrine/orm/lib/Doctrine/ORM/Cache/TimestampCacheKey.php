<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
class TimestampCacheKey extends CacheKey
{
 public function __construct($space)
 {
 parent::__construct((string) $space);
 }
}
