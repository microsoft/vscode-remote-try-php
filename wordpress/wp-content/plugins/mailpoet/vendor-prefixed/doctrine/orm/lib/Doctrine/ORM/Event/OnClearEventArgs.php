<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
class OnClearEventArgs extends EventArgs
{
 private $em;
 private $entityClass;
 public function __construct(EntityManagerInterface $em, $entityClass = null)
 {
 $this->em = $em;
 $this->entityClass = $entityClass;
 }
 public function getEntityManager()
 {
 return $this->em;
 }
 public function getEntityClass()
 {
 return $this->entityClass;
 }
 public function clearsAllEntities()
 {
 return $this->entityClass === null;
 }
}
