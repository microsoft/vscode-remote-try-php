<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
abstract class ObjectManagerDecorator implements ObjectManager
{
 protected $wrapped;
 public function find($className, $id)
 {
 return $this->wrapped->find($className, $id);
 }
 public function persist($object)
 {
 $this->wrapped->persist($object);
 }
 public function remove($object)
 {
 $this->wrapped->remove($object);
 }
 public function merge($object)
 {
 return $this->wrapped->merge($object);
 }
 public function clear($objectName = null)
 {
 $this->wrapped->clear($objectName);
 }
 public function detach($object)
 {
 $this->wrapped->detach($object);
 }
 public function refresh($object)
 {
 $this->wrapped->refresh($object);
 }
 public function flush()
 {
 $this->wrapped->flush();
 }
 public function getRepository($className)
 {
 return $this->wrapped->getRepository($className);
 }
 public function getClassMetadata($className)
 {
 return $this->wrapped->getClassMetadata($className);
 }
 public function getMetadataFactory()
 {
 return $this->wrapped->getMetadataFactory();
 }
 public function initializeObject($obj)
 {
 $this->wrapped->initializeObject($obj);
 }
 public function contains($object)
 {
 return $this->wrapped->contains($object);
 }
}
