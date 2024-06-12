<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
interface MultiGetRegion
{
 public function getMultiple(CollectionCacheEntry $collection);
}
