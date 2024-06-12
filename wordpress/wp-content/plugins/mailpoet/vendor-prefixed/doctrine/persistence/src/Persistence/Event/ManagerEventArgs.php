<?php
namespace MailPoetVendor\Doctrine\Persistence\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
class ManagerEventArgs extends EventArgs
{
 private $objectManager;
 public function __construct(ObjectManager $objectManager)
 {
 $this->objectManager = $objectManager;
 }
 public function getObjectManager()
 {
 return $this->objectManager;
 }
}
