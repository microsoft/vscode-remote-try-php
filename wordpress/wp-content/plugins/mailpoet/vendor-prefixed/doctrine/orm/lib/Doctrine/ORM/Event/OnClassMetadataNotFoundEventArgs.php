<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\Persistence\Event\ManagerEventArgs;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
use function func_num_args;
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
 if (func_num_args() < 1) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9791', 'Calling %s without arguments is deprecated, pass null instead.', __METHOD__);
 }
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
