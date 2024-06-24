<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Callback;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequenceProvider;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
class AnnotationLoader implements LoaderInterface
{
 protected $reader;
 public function __construct(Reader $reader = null)
 {
 $this->reader = $reader;
 }
 public function loadClassMetadata(ClassMetadata $metadata)
 {
 $reflClass = $metadata->getReflectionClass();
 $className = $reflClass->name;
 $success = \false;
 foreach ($this->getAnnotations($reflClass) as $constraint) {
 if ($constraint instanceof GroupSequence) {
 $metadata->setGroupSequence($constraint->groups);
 } elseif ($constraint instanceof GroupSequenceProvider) {
 $metadata->setGroupSequenceProvider(\true);
 } elseif ($constraint instanceof Constraint) {
 $metadata->addConstraint($constraint);
 }
 $success = \true;
 }
 foreach ($reflClass->getProperties() as $property) {
 if ($property->getDeclaringClass()->name === $className) {
 foreach ($this->getAnnotations($property) as $constraint) {
 if ($constraint instanceof Constraint) {
 $metadata->addPropertyConstraint($property->name, $constraint);
 }
 $success = \true;
 }
 }
 }
 foreach ($reflClass->getMethods() as $method) {
 if ($method->getDeclaringClass()->name === $className) {
 foreach ($this->getAnnotations($method) as $constraint) {
 if ($constraint instanceof Callback) {
 $constraint->callback = $method->getName();
 $metadata->addConstraint($constraint);
 } elseif ($constraint instanceof Constraint) {
 if (\preg_match('/^(get|is|has)(.+)$/i', $method->name, $matches)) {
 $metadata->addGetterMethodConstraint(\lcfirst($matches[2]), $matches[0], $constraint);
 } else {
 throw new MappingException(\sprintf('The constraint on "%s::%s()" cannot be added. Constraints can only be added on methods beginning with "get", "is" or "has".', $className, $method->name));
 }
 }
 $success = \true;
 }
 }
 }
 return $success;
 }
 private function getAnnotations(object $reflection) : iterable
 {
 $dedup = [];
 if (\PHP_VERSION_ID >= 80000) {
 foreach ($reflection->getAttributes(GroupSequence::class) as $attribute) {
 $dedup[] = $attribute->newInstance();
 (yield $attribute->newInstance());
 }
 foreach ($reflection->getAttributes(GroupSequenceProvider::class) as $attribute) {
 $dedup[] = $attribute->newInstance();
 (yield $attribute->newInstance());
 }
 foreach ($reflection->getAttributes(Constraint::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
 $dedup[] = $attribute->newInstance();
 (yield $attribute->newInstance());
 }
 }
 if (!$this->reader) {
 return;
 }
 $annotations = [];
 if ($reflection instanceof \ReflectionClass) {
 $annotations = $this->reader->getClassAnnotations($reflection);
 }
 if ($reflection instanceof \ReflectionMethod) {
 $annotations = $this->reader->getMethodAnnotations($reflection);
 }
 if ($reflection instanceof \ReflectionProperty) {
 $annotations = $this->reader->getPropertyAnnotations($reflection);
 }
 foreach ($dedup as $annotation) {
 if ($annotation instanceof Constraint) {
 $annotation->groups;
 // trigger initialization of the "groups" property
 }
 }
 foreach ($annotations as $annotation) {
 if ($annotation instanceof Constraint) {
 $annotation->groups;
 // trigger initialization of the "groups" property
 }
 if (!\in_array($annotation, $dedup, \false)) {
 (yield $annotation);
 }
 }
 }
}
