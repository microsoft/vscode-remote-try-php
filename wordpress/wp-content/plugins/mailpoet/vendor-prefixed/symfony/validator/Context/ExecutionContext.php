<?php
namespace MailPoetVendor\Symfony\Component\Validator\Context;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolation;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationList;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationListInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\MemberMetadata;
use MailPoetVendor\Symfony\Component\Validator\Mapping\MetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\PropertyMetadataInterface;
use MailPoetVendor\Symfony\Component\Validator\Util\PropertyPath;
use MailPoetVendor\Symfony\Component\Validator\Validator\LazyProperty;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
use MailPoetVendor\Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use MailPoetVendor\Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class ExecutionContext implements ExecutionContextInterface
{
 private $validator;
 private $root;
 private $translator;
 private $translationDomain;
 private $violations;
 private $value;
 private $object;
 private $propertyPath = '';
 private $metadata;
 private $group;
 private $constraint;
 private $validatedObjects = [];
 private $validatedConstraints = [];
 private $initializedObjects;
 private $cachedObjectsRefs;
 public function __construct(ValidatorInterface $validator, $root, TranslatorInterface $translator, string $translationDomain = null)
 {
 $this->validator = $validator;
 $this->root = $root;
 $this->translator = $translator;
 $this->translationDomain = $translationDomain;
 $this->violations = new ConstraintViolationList();
 $this->cachedObjectsRefs = new \SplObjectStorage();
 }
 public function setNode($value, ?object $object, MetadataInterface $metadata = null, string $propertyPath)
 {
 $this->value = $value;
 $this->object = $object;
 $this->metadata = $metadata;
 $this->propertyPath = $propertyPath;
 }
 public function setGroup(?string $group)
 {
 $this->group = $group;
 }
 public function setConstraint(Constraint $constraint)
 {
 $this->constraint = $constraint;
 }
 public function addViolation(string $message, array $parameters = [])
 {
 $this->violations->add(new ConstraintViolation($this->translator->trans($message, $parameters, $this->translationDomain), $message, $parameters, $this->root, $this->propertyPath, $this->getValue(), null, null, $this->constraint));
 }
 public function buildViolation(string $message, array $parameters = []) : ConstraintViolationBuilderInterface
 {
 return new ConstraintViolationBuilder($this->violations, $this->constraint, $message, $parameters, $this->root, $this->propertyPath, $this->getValue(), $this->translator, $this->translationDomain);
 }
 public function getViolations() : ConstraintViolationListInterface
 {
 return $this->violations;
 }
 public function getValidator() : ValidatorInterface
 {
 return $this->validator;
 }
 public function getRoot()
 {
 return $this->root;
 }
 public function getValue()
 {
 if ($this->value instanceof LazyProperty) {
 return $this->value->getPropertyValue();
 }
 return $this->value;
 }
 public function getObject()
 {
 return $this->object;
 }
 public function getMetadata() : ?MetadataInterface
 {
 return $this->metadata;
 }
 public function getGroup() : ?string
 {
 return $this->group;
 }
 public function getConstraint() : ?Constraint
 {
 return $this->constraint;
 }
 public function getClassName() : ?string
 {
 return $this->metadata instanceof MemberMetadata || $this->metadata instanceof ClassMetadataInterface ? $this->metadata->getClassName() : null;
 }
 public function getPropertyName() : ?string
 {
 return $this->metadata instanceof PropertyMetadataInterface ? $this->metadata->getPropertyName() : null;
 }
 public function getPropertyPath(string $subPath = '') : string
 {
 return PropertyPath::append($this->propertyPath, $subPath);
 }
 public function markGroupAsValidated(string $cacheKey, string $groupHash)
 {
 if (!isset($this->validatedObjects[$cacheKey])) {
 $this->validatedObjects[$cacheKey] = [];
 }
 $this->validatedObjects[$cacheKey][$groupHash] = \true;
 }
 public function isGroupValidated(string $cacheKey, string $groupHash) : bool
 {
 return isset($this->validatedObjects[$cacheKey][$groupHash]);
 }
 public function markConstraintAsValidated(string $cacheKey, string $constraintHash)
 {
 $this->validatedConstraints[$cacheKey . ':' . $constraintHash] = \true;
 }
 public function isConstraintValidated(string $cacheKey, string $constraintHash) : bool
 {
 return isset($this->validatedConstraints[$cacheKey . ':' . $constraintHash]);
 }
 public function markObjectAsInitialized(string $cacheKey)
 {
 $this->initializedObjects[$cacheKey] = \true;
 }
 public function isObjectInitialized(string $cacheKey) : bool
 {
 return isset($this->initializedObjects[$cacheKey]);
 }
 public function generateCacheKey(object $object) : string
 {
 if (!isset($this->cachedObjectsRefs[$object])) {
 $this->cachedObjectsRefs[$object] = \spl_object_hash($object);
 }
 return $this->cachedObjectsRefs[$object];
 }
 public function __clone()
 {
 $this->violations = clone $this->violations;
 }
}
