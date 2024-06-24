<?php
namespace MailPoetVendor\Symfony\Component\Validator\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Composite;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Existence;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Valid;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContext;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\NoSuchMetadataException;
use MailPoetVendor\Symfony\Component\Validator\Exception\RuntimeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnsupportedMetadataException;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\CascadingStrategy;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\GenericMetadata;
use MailPoetVendor\Symfony\Component\Validator\Mapping\GetterMetadata;
use MailPoetVendor\Symfony\Component\Validator\Mapping\MetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\PropertyMetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\TraversalStrategy;
use MailPoetVendor\Symfony\Component\Validator\ObjectInitializerInterface;
use MailPoetVendor\Symfony\Component\Validator\Util\PropertyPath;
class RecursiveContextualValidator implements ContextualValidatorInterface
{
 private $context;
 private $defaultPropertyPath;
 private $defaultGroups;
 private $metadataFactory;
 private $validatorFactory;
 private $objectInitializers;
 public function __construct(ExecutionContextInterface $context, MetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory, array $objectInitializers = [])
 {
 $this->context = $context;
 $this->defaultPropertyPath = $context->getPropertyPath();
 $this->defaultGroups = [$context->getGroup() ?: Constraint::DEFAULT_GROUP];
 $this->metadataFactory = $metadataFactory;
 $this->validatorFactory = $validatorFactory;
 $this->objectInitializers = $objectInitializers;
 }
 public function atPath(string $path)
 {
 $this->defaultPropertyPath = $this->context->getPropertyPath($path);
 return $this;
 }
 public function validate($value, $constraints = null, $groups = null)
 {
 $groups = $groups ? $this->normalizeGroups($groups) : $this->defaultGroups;
 $previousValue = $this->context->getValue();
 $previousObject = $this->context->getObject();
 $previousMetadata = $this->context->getMetadata();
 $previousPath = $this->context->getPropertyPath();
 $previousGroup = $this->context->getGroup();
 $previousConstraint = null;
 if ($this->context instanceof ExecutionContext || \method_exists($this->context, 'getConstraint')) {
 $previousConstraint = $this->context->getConstraint();
 }
 // If explicit constraints are passed, validate the value against
 // those constraints
 if (null !== $constraints) {
 // You can pass a single constraint or an array of constraints
 // Make sure to deal with an array in the rest of the code
 if (!\is_array($constraints)) {
 $constraints = [$constraints];
 }
 $metadata = new GenericMetadata();
 $metadata->addConstraints($constraints);
 $this->validateGenericNode($value, $previousObject, \is_object($value) ? $this->generateCacheKey($value) : null, $metadata, $this->defaultPropertyPath, $groups, null, TraversalStrategy::IMPLICIT, $this->context);
 $this->context->setNode($previousValue, $previousObject, $previousMetadata, $previousPath);
 $this->context->setGroup($previousGroup);
 if (null !== $previousConstraint) {
 $this->context->setConstraint($previousConstraint);
 }
 return $this;
 }
 // If an object is passed without explicit constraints, validate that
 // object against the constraints defined for the object's class
 if (\is_object($value)) {
 $this->validateObject($value, $this->defaultPropertyPath, $groups, TraversalStrategy::IMPLICIT, $this->context);
 $this->context->setNode($previousValue, $previousObject, $previousMetadata, $previousPath);
 $this->context->setGroup($previousGroup);
 return $this;
 }
 // If an array is passed without explicit constraints, validate each
 // object in the array
 if (\is_array($value)) {
 $this->validateEachObjectIn($value, $this->defaultPropertyPath, $groups, $this->context);
 $this->context->setNode($previousValue, $previousObject, $previousMetadata, $previousPath);
 $this->context->setGroup($previousGroup);
 return $this;
 }
 throw new RuntimeException(\sprintf('Cannot validate values of type "%s" automatically. Please provide a constraint.', \get_debug_type($value)));
 }
 public function validateProperty(object $object, string $propertyName, $groups = null)
 {
 $classMetadata = $this->metadataFactory->getMetadataFor($object);
 if (!$classMetadata instanceof ClassMetadataInterface) {
 throw new ValidatorException(\sprintf('The metadata factory should return instances of "\\Symfony\\Component\\Validator\\Mapping\\ClassMetadataInterface", got: "%s".', \get_debug_type($classMetadata)));
 }
 $propertyMetadatas = $classMetadata->getPropertyMetadata($propertyName);
 $groups = $groups ? $this->normalizeGroups($groups) : $this->defaultGroups;
 $cacheKey = $this->generateCacheKey($object);
 $propertyPath = PropertyPath::append($this->defaultPropertyPath, $propertyName);
 $previousValue = $this->context->getValue();
 $previousObject = $this->context->getObject();
 $previousMetadata = $this->context->getMetadata();
 $previousPath = $this->context->getPropertyPath();
 $previousGroup = $this->context->getGroup();
 foreach ($propertyMetadatas as $propertyMetadata) {
 $propertyValue = $propertyMetadata->getPropertyValue($object);
 $this->validateGenericNode($propertyValue, $object, $cacheKey . ':' . \get_class($object) . ':' . $propertyName, $propertyMetadata, $propertyPath, $groups, null, TraversalStrategy::IMPLICIT, $this->context);
 }
 $this->context->setNode($previousValue, $previousObject, $previousMetadata, $previousPath);
 $this->context->setGroup($previousGroup);
 return $this;
 }
 public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null)
 {
 $classMetadata = $this->metadataFactory->getMetadataFor($objectOrClass);
 if (!$classMetadata instanceof ClassMetadataInterface) {
 throw new ValidatorException(\sprintf('The metadata factory should return instances of "\\Symfony\\Component\\Validator\\Mapping\\ClassMetadataInterface", got: "%s".', \get_debug_type($classMetadata)));
 }
 $propertyMetadatas = $classMetadata->getPropertyMetadata($propertyName);
 $groups = $groups ? $this->normalizeGroups($groups) : $this->defaultGroups;
 if (\is_object($objectOrClass)) {
 $object = $objectOrClass;
 $class = \get_class($object);
 $cacheKey = $this->generateCacheKey($objectOrClass);
 $propertyPath = PropertyPath::append($this->defaultPropertyPath, $propertyName);
 } else {
 // $objectOrClass contains a class name
 $object = null;
 $class = $objectOrClass;
 $cacheKey = null;
 $propertyPath = $this->defaultPropertyPath;
 }
 $previousValue = $this->context->getValue();
 $previousObject = $this->context->getObject();
 $previousMetadata = $this->context->getMetadata();
 $previousPath = $this->context->getPropertyPath();
 $previousGroup = $this->context->getGroup();
 foreach ($propertyMetadatas as $propertyMetadata) {
 $this->validateGenericNode($value, $object, $cacheKey . ':' . $class . ':' . $propertyName, $propertyMetadata, $propertyPath, $groups, null, TraversalStrategy::IMPLICIT, $this->context);
 }
 $this->context->setNode($previousValue, $previousObject, $previousMetadata, $previousPath);
 $this->context->setGroup($previousGroup);
 return $this;
 }
 public function getViolations()
 {
 return $this->context->getViolations();
 }
 protected function normalizeGroups($groups)
 {
 if (\is_array($groups)) {
 return $groups;
 }
 return [$groups];
 }
 private function validateObject(object $object, string $propertyPath, array $groups, int $traversalStrategy, ExecutionContextInterface $context)
 {
 try {
 $classMetadata = $this->metadataFactory->getMetadataFor($object);
 if (!$classMetadata instanceof ClassMetadataInterface) {
 throw new UnsupportedMetadataException(\sprintf('The metadata factory should return instances of "Symfony\\Component\\Validator\\Mapping\\ClassMetadataInterface", got: "%s".', \get_debug_type($classMetadata)));
 }
 $this->validateClassNode($object, $this->generateCacheKey($object), $classMetadata, $propertyPath, $groups, null, $traversalStrategy, $context);
 } catch (NoSuchMetadataException $e) {
 // Rethrow if not Traversable
 if (!$object instanceof \Traversable) {
 throw $e;
 }
 // Rethrow unless IMPLICIT or TRAVERSE
 if (!($traversalStrategy & (TraversalStrategy::IMPLICIT | TraversalStrategy::TRAVERSE))) {
 throw $e;
 }
 $this->validateEachObjectIn($object, $propertyPath, $groups, $context);
 }
 }
 private function validateEachObjectIn(iterable $collection, string $propertyPath, array $groups, ExecutionContextInterface $context)
 {
 foreach ($collection as $key => $value) {
 if (\is_array($value)) {
 // Also traverse nested arrays
 $this->validateEachObjectIn($value, $propertyPath . '[' . $key . ']', $groups, $context);
 continue;
 }
 // Scalar and null values in the collection are ignored
 if (\is_object($value)) {
 $this->validateObject($value, $propertyPath . '[' . $key . ']', $groups, TraversalStrategy::IMPLICIT, $context);
 }
 }
 }
 private function validateClassNode(object $object, ?string $cacheKey, ClassMetadataInterface $metadata, string $propertyPath, array $groups, ?array $cascadedGroups, int $traversalStrategy, ExecutionContextInterface $context)
 {
 $context->setNode($object, $object, $metadata, $propertyPath);
 if (!$context->isObjectInitialized($cacheKey)) {
 foreach ($this->objectInitializers as $initializer) {
 $initializer->initialize($object);
 }
 $context->markObjectAsInitialized($cacheKey);
 }
 foreach ($groups as $key => $group) {
 // If the "Default" group is replaced by a group sequence, remember
 // to cascade the "Default" group when traversing the group
 // sequence
 $defaultOverridden = \false;
 // Use the object hash for group sequences
 $groupHash = \is_object($group) ? $this->generateCacheKey($group, \true) : $group;
 if ($context->isGroupValidated($cacheKey, $groupHash)) {
 // Skip this group when validating the properties and when
 // traversing the object
 unset($groups[$key]);
 continue;
 }
 $context->markGroupAsValidated($cacheKey, $groupHash);
 // Replace the "Default" group by the group sequence defined
 // for the class, if applicable.
 // This is done after checking the cache, so that
 // spl_object_hash() isn't called for this sequence and
 // "Default" is used instead in the cache. This is useful
 // if the getters below return different group sequences in
 // every call.
 if (Constraint::DEFAULT_GROUP === $group) {
 if ($metadata->hasGroupSequence()) {
 // The group sequence is statically defined for the class
 $group = $metadata->getGroupSequence();
 $defaultOverridden = \true;
 } elseif ($metadata->isGroupSequenceProvider()) {
 // The group sequence is dynamically obtained from the validated
 // object
 $group = $object->getGroupSequence();
 $defaultOverridden = \true;
 if (!$group instanceof GroupSequence) {
 $group = new GroupSequence($group);
 }
 }
 }
 // If the groups (=[<G1,G2>,G3,G4]) contain a group sequence
 // (=<G1,G2>), then call validateClassNode() with each entry of the
 // group sequence and abort if necessary (G1, G2)
 if ($group instanceof GroupSequence) {
 $this->stepThroughGroupSequence($object, $object, $cacheKey, $metadata, $propertyPath, $traversalStrategy, $group, $defaultOverridden ? Constraint::DEFAULT_GROUP : null, $context);
 // Skip the group sequence when validating properties, because
 // stepThroughGroupSequence() already validates the properties
 unset($groups[$key]);
 continue;
 }
 $this->validateInGroup($object, $cacheKey, $metadata, $group, $context);
 }
 // If no more groups should be validated for the property nodes,
 // we can safely quit
 if (0 === \count($groups)) {
 return;
 }
 // Validate all properties against their constraints
 foreach ($metadata->getConstrainedProperties() as $propertyName) {
 // If constraints are defined both on the getter of a property as
 // well as on the property itself, then getPropertyMetadata()
 // returns two metadata objects, not just one
 foreach ($metadata->getPropertyMetadata($propertyName) as $propertyMetadata) {
 if (!$propertyMetadata instanceof PropertyMetadataInterface) {
 throw new UnsupportedMetadataException(\sprintf('The property metadata instances should implement "Symfony\\Component\\Validator\\Mapping\\PropertyMetadataInterface", got: "%s".', \get_debug_type($propertyMetadata)));
 }
 if ($propertyMetadata instanceof GetterMetadata) {
 $propertyValue = new LazyProperty(static function () use($propertyMetadata, $object) {
 return $propertyMetadata->getPropertyValue($object);
 });
 } else {
 $propertyValue = $propertyMetadata->getPropertyValue($object);
 }
 $this->validateGenericNode($propertyValue, $object, $cacheKey . ':' . \get_class($object) . ':' . $propertyName, $propertyMetadata, PropertyPath::append($propertyPath, $propertyName), $groups, $cascadedGroups, TraversalStrategy::IMPLICIT, $context);
 }
 }
 // If no specific traversal strategy was requested when this method
 // was called, use the traversal strategy of the class' metadata
 if ($traversalStrategy & TraversalStrategy::IMPLICIT) {
 $traversalStrategy = $metadata->getTraversalStrategy();
 }
 // Traverse only if IMPLICIT or TRAVERSE
 if (!($traversalStrategy & (TraversalStrategy::IMPLICIT | TraversalStrategy::TRAVERSE))) {
 return;
 }
 // If IMPLICIT, stop unless we deal with a Traversable
 if ($traversalStrategy & TraversalStrategy::IMPLICIT && !$object instanceof \Traversable) {
 return;
 }
 // If TRAVERSE, fail if we have no Traversable
 if (!$object instanceof \Traversable) {
 throw new ConstraintDefinitionException(\sprintf('Traversal was enabled for "%s", but this class does not implement "\\Traversable".', \get_debug_type($object)));
 }
 $this->validateEachObjectIn($object, $propertyPath, $groups, $context);
 }
 private function validateGenericNode($value, ?object $object, ?string $cacheKey, ?MetadataInterface $metadata, string $propertyPath, array $groups, ?array $cascadedGroups, int $traversalStrategy, ExecutionContextInterface $context)
 {
 $context->setNode($value, $object, $metadata, $propertyPath);
 foreach ($groups as $key => $group) {
 if ($group instanceof GroupSequence) {
 $this->stepThroughGroupSequence($value, $object, $cacheKey, $metadata, $propertyPath, $traversalStrategy, $group, null, $context);
 // Skip the group sequence when cascading, as the cascading
 // logic is already done in stepThroughGroupSequence()
 unset($groups[$key]);
 continue;
 }
 $this->validateInGroup($value, $cacheKey, $metadata, $group, $context);
 }
 if (0 === \count($groups)) {
 return;
 }
 if (null === $value) {
 return;
 }
 $cascadingStrategy = $metadata->getCascadingStrategy();
 // Quit unless we cascade
 if (!($cascadingStrategy & CascadingStrategy::CASCADE)) {
 return;
 }
 // If no specific traversal strategy was requested when this method
 // was called, use the traversal strategy of the node's metadata
 if ($traversalStrategy & TraversalStrategy::IMPLICIT) {
 $traversalStrategy = $metadata->getTraversalStrategy();
 }
 // The $cascadedGroups property is set, if the "Default" group is
 // overridden by a group sequence
 // See validateClassNode()
 $cascadedGroups = null !== $cascadedGroups && \count($cascadedGroups) > 0 ? $cascadedGroups : $groups;
 if ($value instanceof LazyProperty) {
 $value = $value->getPropertyValue();
 if (null === $value) {
 return;
 }
 }
 if (\is_array($value)) {
 // Arrays are always traversed, independent of the specified
 // traversal strategy
 $this->validateEachObjectIn($value, $propertyPath, $cascadedGroups, $context);
 return;
 }
 if (!\is_object($value)) {
 throw new NoSuchMetadataException(\sprintf('Cannot create metadata for non-objects. Got: "%s".', \gettype($value)));
 }
 $this->validateObject($value, $propertyPath, $cascadedGroups, $traversalStrategy, $context);
 // Currently, the traversal strategy can only be TRAVERSE for a
 // generic node if the cascading strategy is CASCADE. Thus, traversable
 // objects will always be handled within validateObject() and there's
 // nothing more to do here.
 // see GenericMetadata::addConstraint()
 }
 private function stepThroughGroupSequence($value, ?object $object, ?string $cacheKey, ?MetadataInterface $metadata, string $propertyPath, int $traversalStrategy, GroupSequence $groupSequence, ?string $cascadedGroup, ExecutionContextInterface $context)
 {
 $violationCount = \count($context->getViolations());
 $cascadedGroups = $cascadedGroup ? [$cascadedGroup] : null;
 foreach ($groupSequence->groups as $groupInSequence) {
 $groups = (array) $groupInSequence;
 if ($metadata instanceof ClassMetadataInterface) {
 $this->validateClassNode($value, $cacheKey, $metadata, $propertyPath, $groups, $cascadedGroups, $traversalStrategy, $context);
 } else {
 $this->validateGenericNode($value, $object, $cacheKey, $metadata, $propertyPath, $groups, $cascadedGroups, $traversalStrategy, $context);
 }
 // Abort sequence validation if a violation was generated
 if (\count($context->getViolations()) > $violationCount) {
 break;
 }
 }
 }
 private function validateInGroup($value, ?string $cacheKey, MetadataInterface $metadata, string $group, ExecutionContextInterface $context)
 {
 $context->setGroup($group);
 foreach ($metadata->findConstraints($group) as $constraint) {
 if ($constraint instanceof Existence) {
 continue;
 }
 // Prevent duplicate validation of constraints, in the case
 // that constraints belong to multiple validated groups
 if (null !== $cacheKey) {
 $constraintHash = $this->generateCacheKey($constraint, \true);
 // instanceof Valid: In case of using a Valid constraint with many groups
 // it makes a reference object get validated by each group
 if ($constraint instanceof Composite || $constraint instanceof Valid) {
 $constraintHash .= $group;
 }
 if ($context->isConstraintValidated($cacheKey, $constraintHash)) {
 continue;
 }
 $context->markConstraintAsValidated($cacheKey, $constraintHash);
 }
 $context->setConstraint($constraint);
 $validator = $this->validatorFactory->getInstance($constraint);
 $validator->initialize($context);
 if ($value instanceof LazyProperty) {
 $value = $value->getPropertyValue();
 }
 try {
 $validator->validate($value, $constraint);
 } catch (UnexpectedValueException $e) {
 $context->buildViolation('This value should be of type {{ type }}.')->setParameter('{{ type }}', $e->getExpectedType())->addViolation();
 }
 }
 }
 private function generateCacheKey(object $object, bool $dependsOnPropertyPath = \false) : string
 {
 if ($this->context instanceof ExecutionContext) {
 $cacheKey = $this->context->generateCacheKey($object);
 } else {
 $cacheKey = \spl_object_hash($object);
 }
 if ($dependsOnPropertyPath) {
 $cacheKey .= $this->context->getPropertyPath();
 }
 return $cacheKey;
 }
}
