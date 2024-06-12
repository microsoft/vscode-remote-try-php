<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Repository;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
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
 return new $repositoryClassName($entityManager, $metadata);
 }
}
