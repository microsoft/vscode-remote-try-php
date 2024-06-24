<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\Collection\CachedCollectionPersister;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\Entity\CachedEntityPersister;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\CollectionPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
interface CacheFactory
{
 public function buildCachedEntityPersister(EntityManagerInterface $em, EntityPersister $persister, ClassMetadata $metadata);
 public function buildCachedCollectionPersister(EntityManagerInterface $em, CollectionPersister $persister, array $mapping);
 public function buildQueryCache(EntityManagerInterface $em, $regionName = null);
 public function buildEntityHydrator(EntityManagerInterface $em, ClassMetadata $metadata);
 public function buildCollectionHydrator(EntityManagerInterface $em, array $mapping);
 public function getRegion(array $cache);
 public function getTimestampRegion();
 public function createCache(EntityManagerInterface $entityManager);
}
