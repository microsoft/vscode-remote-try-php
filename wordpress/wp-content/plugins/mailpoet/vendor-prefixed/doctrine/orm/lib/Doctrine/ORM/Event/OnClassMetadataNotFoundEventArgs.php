<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Event\ManagerEventArgs;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
class OnClassMetadataNotFoundEventArgs extends ManagerEventArgs
{
 private $className;
 private $foundMetadata;
 public function __construct($className, ObjectManager $objectManager)
 {
 $this->className = (string) $className;
 parent::__construct($objectManager);
 }
 public function setFoundMetadata(?ClassMetadata $classMetadata = null)
 {
 $this->foundMetadata = $classMetadata;
 }
 public function getFoundMetadata()
 {
 return $this->foundMetadata;
 }
 public function getClassName()
 {
 return $this->className;
 }
}
