<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\EntityHydrator;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\CachedPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
interface CachedEntityPersister extends CachedPersister, EntityPersister
{
 public function getEntityHydrator();
 public function storeEntityCache($entity, EntityCacheKey $key);
}
