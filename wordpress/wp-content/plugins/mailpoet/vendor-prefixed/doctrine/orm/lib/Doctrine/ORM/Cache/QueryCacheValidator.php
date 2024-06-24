<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
interface QueryCacheValidator
{
 public function isValid(QueryCacheKey $key, QueryCacheEntry $entry);
}
