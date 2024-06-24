<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Composite;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
abstract class MemberMetadata extends GenericMetadata implements PropertyMetadataInterface
{
 public $class;
 public $name;
 public $property;
 private $reflMember = [];
 public function __construct(string $class, string $name, string $property)
 {
 $this->class = $class;
 $this->name = $name;
 $this->property = $property;
 }
 public function addConstraint(Constraint $constraint)
 {
 $this->checkConstraint($constraint);
 parent::addConstraint($constraint);
 return $this;
 }
 public function __sleep()
 {
 return \array_merge(parent::__sleep(), ['class', 'name', 'property']);
 }
 public function getName()
 {
 return $this->name;
 }
 public function getClassName()
 {
 return $this->class;
 }
 public function getPropertyName()
 {
 return $this->property;
 }
 public function isPublic($objectOrClassName)
 {
 return $this->getReflectionMember($objectOrClassName)->isPublic();
 }
 public function isProtected($objectOrClassName)
 {
 return $this->getReflectionMember($objectOrClassName)->isProtected();
 }
 public function isPrivate($objectOrClassName)
 {
 return $this->getReflectionMember($objectOrClassName)->isPrivate();
 }
 public function getReflectionMember($objectOrClassName)
 {
 $className = \is_string($objectOrClassName) ? $objectOrClassName : \get_class($objectOrClassName);
 if (!isset($this->reflMember[$className])) {
 $this->reflMember[$className] = $this->newReflectionMember($objectOrClassName);
 }
 return $this->reflMember[$className];
 }
 protected abstract function newReflectionMember($objectOrClassName);
 private function checkConstraint(Constraint $constraint)
 {
 if (!\in_array(Constraint::PROPERTY_CONSTRAINT, (array) $constraint->getTargets(), \true)) {
 throw new ConstraintDefinitionException(\sprintf('The constraint "%s" cannot be put on properties or getters.', \get_debug_type($constraint)));
 }
 if ($constraint instanceof Composite) {
 foreach ($constraint->getNestedConstraints() as $nestedConstraint) {
 $this->checkConstraint($nestedConstraint);
 }
 }
 }
}
