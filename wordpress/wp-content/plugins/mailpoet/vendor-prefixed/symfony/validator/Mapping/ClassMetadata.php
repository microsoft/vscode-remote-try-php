<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Cascade;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Composite;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Traverse;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Valid;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\GroupDefinitionException;
class ClassMetadata extends GenericMetadata implements ClassMetadataInterface
{
 public $name;
 public $defaultGroup;
 public $members = [];
 public $properties = [];
 public $getters = [];
 public $groupSequence = [];
 public $groupSequenceProvider = \false;
 public $traversalStrategy = TraversalStrategy::IMPLICIT;
 private $reflClass;
 public function __construct(string $class)
 {
 $this->name = $class;
 // class name without namespace
 if (\false !== ($nsSep = \strrpos($class, '\\'))) {
 $this->defaultGroup = \substr($class, $nsSep + 1);
 } else {
 $this->defaultGroup = $class;
 }
 }
 public function __sleep()
 {
 $parentProperties = parent::__sleep();
 // Don't store the cascading strategy. Classes never cascade.
 unset($parentProperties[\array_search('cascadingStrategy', $parentProperties)]);
 return \array_merge($parentProperties, ['getters', 'groupSequence', 'groupSequenceProvider', 'members', 'name', 'properties', 'defaultGroup']);
 }
 public function getClassName()
 {
 return $this->name;
 }
 public function getDefaultGroup()
 {
 return $this->defaultGroup;
 }
 public function addConstraint(Constraint $constraint)
 {
 $this->checkConstraint($constraint);
 if ($constraint instanceof Traverse) {
 if ($constraint->traverse) {
 // If traverse is true, traversal should be explicitly enabled
 $this->traversalStrategy = TraversalStrategy::TRAVERSE;
 } else {
 // If traverse is false, traversal should be explicitly disabled
 $this->traversalStrategy = TraversalStrategy::NONE;
 }
 // The constraint is not added
 return $this;
 }
 if ($constraint instanceof Cascade) {
 if (\PHP_VERSION_ID < 70400) {
 throw new ConstraintDefinitionException(\sprintf('The constraint "%s" requires PHP 7.4.', Cascade::class));
 }
 $this->cascadingStrategy = CascadingStrategy::CASCADE;
 foreach ($this->getReflectionClass()->getProperties() as $property) {
 if ($property->hasType() && ('array' === ($type = $property->getType()->getName()) || \class_exists($type))) {
 $this->addPropertyConstraint($property->getName(), new Valid());
 }
 }
 // The constraint is not added
 return $this;
 }
 $constraint->addImplicitGroupName($this->getDefaultGroup());
 parent::addConstraint($constraint);
 return $this;
 }
 public function addPropertyConstraint(string $property, Constraint $constraint)
 {
 if (!isset($this->properties[$property])) {
 $this->properties[$property] = new PropertyMetadata($this->getClassName(), $property);
 $this->addPropertyMetadata($this->properties[$property]);
 }
 $constraint->addImplicitGroupName($this->getDefaultGroup());
 $this->properties[$property]->addConstraint($constraint);
 return $this;
 }
 public function addPropertyConstraints(string $property, array $constraints)
 {
 foreach ($constraints as $constraint) {
 $this->addPropertyConstraint($property, $constraint);
 }
 return $this;
 }
 public function addGetterConstraint(string $property, Constraint $constraint)
 {
 if (!isset($this->getters[$property])) {
 $this->getters[$property] = new GetterMetadata($this->getClassName(), $property);
 $this->addPropertyMetadata($this->getters[$property]);
 }
 $constraint->addImplicitGroupName($this->getDefaultGroup());
 $this->getters[$property]->addConstraint($constraint);
 return $this;
 }
 public function addGetterMethodConstraint(string $property, string $method, Constraint $constraint)
 {
 if (!isset($this->getters[$property])) {
 $this->getters[$property] = new GetterMetadata($this->getClassName(), $property, $method);
 $this->addPropertyMetadata($this->getters[$property]);
 }
 $constraint->addImplicitGroupName($this->getDefaultGroup());
 $this->getters[$property]->addConstraint($constraint);
 return $this;
 }
 public function addGetterConstraints(string $property, array $constraints)
 {
 foreach ($constraints as $constraint) {
 $this->addGetterConstraint($property, $constraint);
 }
 return $this;
 }
 public function addGetterMethodConstraints(string $property, string $method, array $constraints)
 {
 foreach ($constraints as $constraint) {
 $this->addGetterMethodConstraint($property, $method, $constraint);
 }
 return $this;
 }
 public function mergeConstraints(self $source)
 {
 if ($source->isGroupSequenceProvider()) {
 $this->setGroupSequenceProvider(\true);
 }
 foreach ($source->getConstraints() as $constraint) {
 $this->addConstraint(clone $constraint);
 }
 foreach ($source->getConstrainedProperties() as $property) {
 foreach ($source->getPropertyMetadata($property) as $member) {
 $member = clone $member;
 foreach ($member->getConstraints() as $constraint) {
 if (\in_array($constraint::DEFAULT_GROUP, $constraint->groups, \true)) {
 $member->constraintsByGroup[$this->getDefaultGroup()][] = $constraint;
 }
 $constraint->addImplicitGroupName($this->getDefaultGroup());
 }
 $this->addPropertyMetadata($member);
 if ($member instanceof MemberMetadata && !$member->isPrivate($this->name)) {
 $property = $member->getPropertyName();
 if ($member instanceof PropertyMetadata && !isset($this->properties[$property])) {
 $this->properties[$property] = $member;
 } elseif ($member instanceof GetterMetadata && !isset($this->getters[$property])) {
 $this->getters[$property] = $member;
 }
 }
 }
 }
 }
 public function hasPropertyMetadata(string $property)
 {
 return \array_key_exists($property, $this->members);
 }
 public function getPropertyMetadata(string $property)
 {
 return $this->members[$property] ?? [];
 }
 public function getConstrainedProperties()
 {
 return \array_keys($this->members);
 }
 public function setGroupSequence($groupSequence)
 {
 if ($this->isGroupSequenceProvider()) {
 throw new GroupDefinitionException('Defining a static group sequence is not allowed with a group sequence provider.');
 }
 if (\is_array($groupSequence)) {
 $groupSequence = new GroupSequence($groupSequence);
 }
 if (\in_array(Constraint::DEFAULT_GROUP, $groupSequence->groups, \true)) {
 throw new GroupDefinitionException(\sprintf('The group "%s" is not allowed in group sequences.', Constraint::DEFAULT_GROUP));
 }
 if (!\in_array($this->getDefaultGroup(), $groupSequence->groups, \true)) {
 throw new GroupDefinitionException(\sprintf('The group "%s" is missing in the group sequence.', $this->getDefaultGroup()));
 }
 $this->groupSequence = $groupSequence;
 return $this;
 }
 public function hasGroupSequence()
 {
 return $this->groupSequence && \count($this->groupSequence->groups) > 0;
 }
 public function getGroupSequence()
 {
 return $this->groupSequence;
 }
 public function getReflectionClass()
 {
 if (!$this->reflClass) {
 $this->reflClass = new \ReflectionClass($this->getClassName());
 }
 return $this->reflClass;
 }
 public function setGroupSequenceProvider(bool $active)
 {
 if ($this->hasGroupSequence()) {
 throw new GroupDefinitionException('Defining a group sequence provider is not allowed with a static group sequence.');
 }
 if (!$this->getReflectionClass()->implementsInterface('MailPoetVendor\\Symfony\\Component\\Validator\\GroupSequenceProviderInterface')) {
 throw new GroupDefinitionException(\sprintf('Class "%s" must implement GroupSequenceProviderInterface.', $this->name));
 }
 $this->groupSequenceProvider = $active;
 }
 public function isGroupSequenceProvider()
 {
 return $this->groupSequenceProvider;
 }
 public function getCascadingStrategy()
 {
 return $this->cascadingStrategy;
 }
 private function addPropertyMetadata(PropertyMetadataInterface $metadata)
 {
 $property = $metadata->getPropertyName();
 $this->members[$property][] = $metadata;
 }
 private function checkConstraint(Constraint $constraint)
 {
 if (!\in_array(Constraint::CLASS_CONSTRAINT, (array) $constraint->getTargets(), \true)) {
 throw new ConstraintDefinitionException(\sprintf('The constraint "%s" cannot be put on classes.', \get_debug_type($constraint)));
 }
 if ($constraint instanceof Composite) {
 foreach ($constraint->getNestedConstraints() as $nestedConstraint) {
 $this->checkConstraint($nestedConstraint);
 }
 }
 }
}
