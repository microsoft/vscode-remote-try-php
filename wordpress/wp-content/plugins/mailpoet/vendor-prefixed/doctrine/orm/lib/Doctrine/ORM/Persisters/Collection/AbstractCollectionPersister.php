<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Collection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\QuoteStrategy;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
abstract class AbstractCollectionPersister implements CollectionPersister
{
 protected $em;
 protected $conn;
 protected $uow;
 protected $platform;
 protected $quoteStrategy;
 public function __construct(EntityManagerInterface $em)
 {
 $this->em = $em;
 $this->uow = $em->getUnitOfWork();
 $this->conn = $em->getConnection();
 $this->platform = $this->conn->getDatabasePlatform();
 $this->quoteStrategy = $em->getConfiguration()->getQuoteStrategy();
 }
 protected function isValidEntityState($entity)
 {
 $entityState = $this->uow->getEntityState($entity, UnitOfWork::STATE_NEW);
 if ($entityState === UnitOfWork::STATE_NEW) {
 return \false;
 }
 // If Entity is scheduled for inclusion, it is not in this collection.
 // We can assure that because it would have return true before on array check
 return !($entityState === UnitOfWork::STATE_MANAGED && $this->uow->isScheduledForInsert($entity));
 }
}
