<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException;
class GetterMetadata extends MemberMetadata
{
 public function __construct(string $class, string $property, string $method = null)
 {
 if (null === $method) {
 $getMethod = 'get' . \ucfirst($property);
 $isMethod = 'is' . \ucfirst($property);
 $hasMethod = 'has' . \ucfirst($property);
 if (\method_exists($class, $getMethod)) {
 $method = $getMethod;
 } elseif (\method_exists($class, $isMethod)) {
 $method = $isMethod;
 } elseif (\method_exists($class, $hasMethod)) {
 $method = $hasMethod;
 } else {
 throw new ValidatorException(\sprintf('Neither of these methods exist in class "%s": "%s", "%s", "%s".', $class, $getMethod, $isMethod, $hasMethod));
 }
 } elseif (!\method_exists($class, $method)) {
 throw new ValidatorException(\sprintf('The "%s()" method does not exist in class "%s".', $method, $class));
 }
 parent::__construct($class, $method, $property);
 }
 public function getPropertyValue($object)
 {
 return $this->newReflectionMember($object)->invoke($object);
 }
 protected function newReflectionMember($objectOrClassName)
 {
 return new \ReflectionMethod($objectOrClassName, $this->getName());
 }
}
