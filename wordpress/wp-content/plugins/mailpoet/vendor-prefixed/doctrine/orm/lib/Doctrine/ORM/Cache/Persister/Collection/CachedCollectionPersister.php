<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Collection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\CachedPersister;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\CollectionPersister;
interface CachedCollectionPersister extends CachedPersister, CollectionPersister
{
 public function getSourceEntityMetadata();
 public function getTargetEntityMetadata();
 public function loadCollectionCache(PersistentCollection $collection, CollectionCacheKey $key);
 public function storeCollectionCache(CollectionCacheKey $key, $elements);
}
