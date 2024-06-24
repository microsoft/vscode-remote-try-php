<?php
namespace MailPoetVendor\Doctrine\Common\Proxy;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use MailPoetVendor\Doctrine\Common\Proxy\Exception\OutOfBoundsException;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadataFactory;
use function class_exists;
use function file_exists;
use function filemtime;
use function in_array;
abstract class AbstractProxyFactory
{
 public const AUTOGENERATE_NEVER = 0;
 public const AUTOGENERATE_ALWAYS = 1;
 public const AUTOGENERATE_FILE_NOT_EXISTS = 2;
 public const AUTOGENERATE_EVAL = 3;
 public const AUTOGENERATE_FILE_NOT_EXISTS_OR_CHANGED = 4;
 private const AUTOGENERATE_MODES = [self::AUTOGENERATE_NEVER, self::AUTOGENERATE_ALWAYS, self::AUTOGENERATE_FILE_NOT_EXISTS, self::AUTOGENERATE_EVAL, self::AUTOGENERATE_FILE_NOT_EXISTS_OR_CHANGED];
 private $metadataFactory;
 private $proxyGenerator;
 private $autoGenerate;
 private $definitions = [];
 public function __construct(ProxyGenerator $proxyGenerator, ClassMetadataFactory $metadataFactory, $autoGenerate)
 {
 $this->proxyGenerator = $proxyGenerator;
 $this->metadataFactory = $metadataFactory;
 $this->autoGenerate = (int) $autoGenerate;
 if (!in_array($this->autoGenerate, self::AUTOGENERATE_MODES, \true)) {
 throw InvalidArgumentException::invalidAutoGenerateMode($autoGenerate);
 }
 }
 public function getProxy($className, array $identifier)
 {
 $definition = $this->definitions[$className] ?? $this->getProxyDefinition($className);
 $fqcn = $definition->proxyClassName;
 $proxy = new $fqcn($definition->initializer, $definition->cloner);
 foreach ($definition->identifierFields as $idField) {
 if (!isset($identifier[$idField])) {
 throw OutOfBoundsException::missingPrimaryKeyValue($className, $idField);
 }
 $definition->reflectionFields[$idField]->setValue($proxy, $identifier[$idField]);
 }
 return $proxy;
 }
 public function generateProxyClasses(array $classes, $proxyDir = null)
 {
 $generated = 0;
 foreach ($classes as $class) {
 if ($this->skipClass($class)) {
 continue;
 }
 $proxyFileName = $this->proxyGenerator->getProxyFileName($class->getName(), $proxyDir);
 $this->proxyGenerator->generateProxyClass($class, $proxyFileName);
 $generated += 1;
 }
 return $generated;
 }
 public function resetUninitializedProxy(Proxy $proxy)
 {
 if ($proxy->__isInitialized()) {
 throw InvalidArgumentException::unitializedProxyExpected($proxy);
 }
 $className = ClassUtils::getClass($proxy);
 $definition = $this->definitions[$className] ?? $this->getProxyDefinition($className);
 $proxy->__setInitializer($definition->initializer);
 $proxy->__setCloner($definition->cloner);
 return $proxy;
 }
 private function getProxyDefinition($className)
 {
 $classMetadata = $this->metadataFactory->getMetadataFor($className);
 $className = $classMetadata->getName();
 // aliases and case sensitivity
 $this->definitions[$className] = $this->createProxyDefinition($className);
 $proxyClassName = $this->definitions[$className]->proxyClassName;
 if (!class_exists($proxyClassName, \false)) {
 $fileName = $this->proxyGenerator->getProxyFileName($className);
 switch ($this->autoGenerate) {
 case self::AUTOGENERATE_NEVER:
 require $fileName;
 break;
 case self::AUTOGENERATE_FILE_NOT_EXISTS:
 if (!file_exists($fileName)) {
 $this->proxyGenerator->generateProxyClass($classMetadata, $fileName);
 }
 require $fileName;
 break;
 case self::AUTOGENERATE_ALWAYS:
 $this->proxyGenerator->generateProxyClass($classMetadata, $fileName);
 require $fileName;
 break;
 case self::AUTOGENERATE_EVAL:
 $this->proxyGenerator->generateProxyClass($classMetadata, \false);
 break;
 case self::AUTOGENERATE_FILE_NOT_EXISTS_OR_CHANGED:
 if (!file_exists($fileName) || filemtime($fileName) < filemtime($classMetadata->getReflectionClass()->getFileName())) {
 $this->proxyGenerator->generateProxyClass($classMetadata, $fileName);
 }
 require $fileName;
 break;
 }
 }
 return $this->definitions[$className];
 }
 protected abstract function skipClass(ClassMetadata $metadata);
 protected abstract function createProxyDefinition($className);
}
