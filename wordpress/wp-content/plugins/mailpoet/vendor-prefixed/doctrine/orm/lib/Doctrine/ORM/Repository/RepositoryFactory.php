<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Repository;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\Persistence\ObjectRepository;
interface RepositoryFactory
{
 public function getRepository(EntityManagerInterface $entityManager, $entityName);
}
