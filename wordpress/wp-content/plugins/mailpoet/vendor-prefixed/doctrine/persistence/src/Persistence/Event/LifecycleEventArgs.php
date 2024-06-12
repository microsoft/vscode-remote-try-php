<?php
namespace MailPoetVendor\Doctrine\Persistence\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
class LifecycleEventArgs extends EventArgs
{
 private $objectManager;
 private $object;
 public function __construct($object, ObjectManager $objectManager)
 {
 $this->object = $object;
 $this->objectManager = $objectManager;
 }
 public function getEntity()
 {
 return $this->object;
 }
 public function getObject()
 {
 return $this->object;
 }
 public function getObjectManager()
 {
 return $this->objectManager;
 }
}
