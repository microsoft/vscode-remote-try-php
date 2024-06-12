<?php
namespace MailPoetVendor\Symfony\Component\Validator\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\ObjectInitializerInterface;
class RecursiveValidator implements ValidatorInterface
{
 protected $contextFactory;
 protected $metadataFactory;
 protected $validatorFactory;
 protected $objectInitializers;
 public function __construct(ExecutionContextFactoryInterface $contextFactory, MetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory, array $objectInitializers = [])
 {
 $this->contextFactory = $contextFactory;
 $this->metadataFactory = $metadataFactory;
 $this->validatorFactory = $validatorFactory;
 $this->objectInitializers = $objectInitializers;
 }
 public function startContext($root = null)
 {
 return new RecursiveContextualValidator($this->contextFactory->createContext($this, $root), $this->metadataFactory, $this->validatorFactory, $this->objectInitializers);
 }
 public function inContext(ExecutionContextInterface $context)
 {
 return new RecursiveContextualValidator($context, $this->metadataFactory, $this->validatorFactory, $this->objectInitializers);
 }
 public function getMetadataFor($object)
 {
 return $this->metadataFactory->getMetadataFor($object);
 }
 public function hasMetadataFor($object)
 {
 return $this->metadataFactory->hasMetadataFor($object);
 }
 public function validate($value, $constraints = null, $groups = null)
 {
 return $this->startContext($value)->validate($value, $constraints, $groups)->getViolations();
 }
 public function validateProperty(object $object, string $propertyName, $groups = null)
 {
 return $this->startContext($object)->validateProperty($object, $propertyName, $groups)->getViolations();
 }
 public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null)
 {
 // If a class name is passed, take $value as root
 return $this->startContext(\is_object($objectOrClass) ? $objectOrClass : $value)->validatePropertyValue($objectOrClass, $propertyName, $value, $groups)->getViolations();
 }
}
