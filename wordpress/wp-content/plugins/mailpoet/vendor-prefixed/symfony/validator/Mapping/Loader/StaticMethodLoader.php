<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
class StaticMethodLoader implements LoaderInterface
{
 protected $methodName;
 public function __construct(string $methodName = 'loadValidatorMetadata')
 {
 $this->methodName = $methodName;
 }
 public function loadClassMetadata(ClassMetadata $metadata)
 {
 $reflClass = $metadata->getReflectionClass();
 if (!$reflClass->isInterface() && $reflClass->hasMethod($this->methodName)) {
 $reflMethod = $reflClass->getMethod($this->methodName);
 if ($reflMethod->isAbstract()) {
 return \false;
 }
 if (!$reflMethod->isStatic()) {
 throw new MappingException(\sprintf('The method "%s::%s()" should be static.', $reflClass->name, $this->methodName));
 }
 if ($reflMethod->getDeclaringClass()->name != $reflClass->name) {
 return \false;
 }
 $reflMethod->invoke(null, $metadata);
 return \true;
 }
 return \false;
 }
}
