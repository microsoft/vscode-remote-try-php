<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
interface EntityHydrator
{
 public function buildCacheEntry(ClassMetadata $metadata, EntityCacheKey $key, $entity);
 public function loadCacheEntry(ClassMetadata $metadata, EntityCacheKey $key, EntityCacheEntry $entry, $entity = null);
}
