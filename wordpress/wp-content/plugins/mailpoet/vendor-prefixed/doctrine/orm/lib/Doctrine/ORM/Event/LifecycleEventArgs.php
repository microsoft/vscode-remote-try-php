<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
class LifecycleEventArgs extends BaseLifecycleEventArgs
{
 public function getEntity()
 {
 return $this->getObject();
 }
 public function getEntityManager()
 {
 return $this->getObjectManager();
 }
}
