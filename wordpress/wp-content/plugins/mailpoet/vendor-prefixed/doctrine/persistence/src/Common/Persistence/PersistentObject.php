<?php
namespace MailPoetVendor\Doctrine\Common\Persistence;
if (!defined('ABSPATH')) exit;
use BadMethodCallException;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
use MailPoetVendor\Doctrine\Persistence\ObjectManagerAware;
use InvalidArgumentException;
use RuntimeException;
use function lcfirst;
use function substr;
abstract class PersistentObject implements ObjectManagerAware
{
 private static $objectManager = null;
 private $cm = null;
 public static function setObjectManager(?ObjectManager $objectManager = null)
 {
 self::$objectManager = $objectManager;
 }
 public static function getObjectManager()
 {
 return self::$objectManager;
 }
 public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata)
 {
 if ($objectManager !== self::$objectManager) {
 throw new RuntimeException('Trying to use PersistentObject with different ObjectManager instances. ' . 'Was PersistentObject::setObjectManager() called?');
 }
 $this->cm = $classMetadata;
 }
 private function set($field, $args)
 {
 if ($this->cm->hasField($field) && !$this->cm->isIdentifier($field)) {
 $this->{$field} = $args[0];
 } elseif ($this->cm->hasAssociation($field) && $this->cm->isSingleValuedAssociation($field)) {
 $targetClass = $this->cm->getAssociationTargetClass($field);
 if ($targetClass !== null && !$args[0] instanceof $targetClass && $args[0] !== null) {
 throw new InvalidArgumentException("Expected persistent object of type '" . $targetClass . "'");
 }
 $this->{$field} = $args[0];
 $this->completeOwningSide($field, $targetClass, $args[0]);
 } else {
 throw new BadMethodCallException("no field with name '" . $field . "' exists on '" . $this->cm->getName() . "'");
 }
 }
 private function get($field)
 {
 if ($this->cm->hasField($field) || $this->cm->hasAssociation($field)) {
 return $this->{$field};
 }
 throw new BadMethodCallException("no field with name '" . $field . "' exists on '" . $this->cm->getName() . "'");
 }
 private function completeOwningSide($field, $targetClass, $targetObject)
 {
 // add this object on the owning side as well, for obvious infinite recursion
 // reasons this is only done when called on the inverse side.
 if (!$this->cm->isAssociationInverseSide($field)) {
 return;
 }
 $mappedByField = $this->cm->getAssociationMappedByTargetField($field);
 $targetMetadata = self::$objectManager->getClassMetadata($targetClass);
 $setter = ($targetMetadata->isCollectionValuedAssociation($mappedByField) ? 'add' : 'set') . $mappedByField;
 $targetObject->{$setter}($this);
 }
 private function add($field, $args)
 {
 if (!$this->cm->hasAssociation($field) || !$this->cm->isCollectionValuedAssociation($field)) {
 throw new BadMethodCallException('There is no method add' . $field . '() on ' . $this->cm->getName());
 }
 $targetClass = $this->cm->getAssociationTargetClass($field);
 if ($targetClass !== null && !$args[0] instanceof $targetClass) {
 throw new InvalidArgumentException("Expected persistent object of type '" . $targetClass . "'");
 }
 if (!$this->{$field} instanceof Collection) {
 $this->{$field} = new ArrayCollection($this->{$field} ?: []);
 }
 $this->{$field}->add($args[0]);
 $this->completeOwningSide($field, $targetClass, $args[0]);
 }
 private function initializeDoctrine()
 {
 if ($this->cm !== null) {
 return;
 }
 if (!self::$objectManager) {
 throw new RuntimeException('No runtime object manager set. Call PersistentObject#setObjectManager().');
 }
 $this->cm = self::$objectManager->getClassMetadata(static::class);
 }
 public function __call($method, $args)
 {
 $this->initializeDoctrine();
 $command = substr($method, 0, 3);
 $field = lcfirst(substr($method, 3));
 if ($command === 'set') {
 $this->set($field, $args);
 } elseif ($command === 'get') {
 return $this->get($field);
 } elseif ($command === 'add') {
 $this->add($field, $args);
 } else {
 throw new BadMethodCallException('There is no method ' . $method . ' on ' . $this->cm->getName());
 }
 }
}
