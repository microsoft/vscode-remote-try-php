<?php
namespace MailPoetVendor\Doctrine\Persistence\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
class OnClearEventArgs extends EventArgs
{
 private $objectManager;
 private $entityClass;
 public function __construct($objectManager, $entityClass = null)
 {
 $this->objectManager = $objectManager;
 $this->entityClass = $entityClass;
 }
 public function getObjectManager()
 {
 return $this->objectManager;
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
