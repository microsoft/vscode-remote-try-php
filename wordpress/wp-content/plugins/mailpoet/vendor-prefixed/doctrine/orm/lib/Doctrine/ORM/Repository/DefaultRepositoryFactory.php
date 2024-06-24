<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Repository;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\EntityRepository;
use MailPoetVendor\Doctrine\Persistence\ObjectRepository;
use function spl_object_id;
final class DefaultRepositoryFactory implements RepositoryFactory
{
 private $repositoryList = [];
 public function getRepository(EntityManagerInterface $entityManager, $entityName) : ObjectRepository
 {
 $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_id($entityManager);
 if (isset($this->repositoryList[$repositoryHash])) {
 return $this->repositoryList[$repositoryHash];
 }
 return $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
 }
 private function createRepository(EntityManagerInterface $entityManager, string $entityName) : ObjectRepository
 {
 $metadata = $entityManager->getClassMetadata($entityName);
 $repositoryClassName = $metadata->customRepositoryClassName ?: $entityManager->getConfiguration()->getDefaultRepositoryClassName();
 $repository = new $repositoryClassName($entityManager, $metadata);
 if (!$repository instanceof EntityRepository) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9533', 'Configuring %s as repository class is deprecated because it does not extend %s.', $repositoryClassName, EntityRepository::class);
 }
 return $repository;
 }
}
