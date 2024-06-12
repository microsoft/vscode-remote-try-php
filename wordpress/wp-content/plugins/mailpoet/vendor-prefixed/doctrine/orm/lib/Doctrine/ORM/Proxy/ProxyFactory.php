<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Proxy;
if (!defined('ABSPATH')) exit;
use Closure;
use MailPoetVendor\Doctrine\Common\Proxy\AbstractProxyFactory;
use MailPoetVendor\Doctrine\Common\Proxy\Proxy as BaseProxy;
use MailPoetVendor\Doctrine\Common\Proxy\ProxyDefinition;
use MailPoetVendor\Doctrine\Common\Proxy\ProxyGenerator;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\EntityNotFoundException;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use MailPoetVendor\Doctrine\ORM\Utility\IdentifierFlattener;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
class ProxyFactory extends AbstractProxyFactory
{
 private $em;
 private $uow;
 private $proxyNs;
 private $identifierFlattener;
 public function __construct(EntityManagerInterface $em, $proxyDir, $proxyNs, $autoGenerate = AbstractProxyFactory::AUTOGENERATE_NEVER)
 {
 $proxyGenerator = new ProxyGenerator($proxyDir, $proxyNs);
 $proxyGenerator->setPlaceholder('baseProxyInterface', Proxy::class);
 parent::__construct($proxyGenerator, $em->getMetadataFactory(), $autoGenerate);
 $this->em = $em;
 $this->uow = $em->getUnitOfWork();
 $this->proxyNs = $proxyNs;
 $this->identifierFlattener = new IdentifierFlattener($this->uow, $em->getMetadataFactory());
 }
 protected function skipClass(ClassMetadata $metadata)
 {
 return $metadata->isMappedSuperclass || $metadata->isEmbeddedClass || $metadata->getReflectionClass()->isAbstract();
 }
 protected function createProxyDefinition($className)
 {
 $classMetadata = $this->em->getClassMetadata($className);
 $entityPersister = $this->uow->getEntityPersister($className);
 return new ProxyDefinition(ClassUtils::generateProxyClassName($className, $this->proxyNs), $classMetadata->getIdentifierFieldNames(), $classMetadata->getReflectionProperties(), $this->createInitializer($classMetadata, $entityPersister), $this->createCloner($classMetadata, $entityPersister));
 }
 private function createInitializer(ClassMetadata $classMetadata, EntityPersister $entityPersister) : Closure
 {
 $wakeupProxy = $classMetadata->getReflectionClass()->hasMethod('__wakeup');
 return function (BaseProxy $proxy) use($entityPersister, $classMetadata, $wakeupProxy) : void {
 $initializer = $proxy->__getInitializer();
 $cloner = $proxy->__getCloner();
 $proxy->__setInitializer(null);
 $proxy->__setCloner(null);
 if ($proxy->__isInitialized()) {
 return;
 }
 $properties = $proxy->__getLazyProperties();
 foreach ($properties as $propertyName => $property) {
 if (!isset($proxy->{$propertyName})) {
 $proxy->{$propertyName} = $properties[$propertyName];
 }
 }
 $proxy->__setInitialized(\true);
 if ($wakeupProxy) {
 $proxy->__wakeup();
 }
 $identifier = $classMetadata->getIdentifierValues($proxy);
 if ($entityPersister->loadById($identifier, $proxy) === null) {
 $proxy->__setInitializer($initializer);
 $proxy->__setCloner($cloner);
 $proxy->__setInitialized(\false);
 throw EntityNotFoundException::fromClassNameAndIdentifier($classMetadata->getName(), $this->identifierFlattener->flattenIdentifier($classMetadata, $identifier));
 }
 };
 }
 private function createCloner(ClassMetadata $classMetadata, EntityPersister $entityPersister) : Closure
 {
 return function (BaseProxy $proxy) use($entityPersister, $classMetadata) : void {
 if ($proxy->__isInitialized()) {
 return;
 }
 $proxy->__setInitialized(\true);
 $proxy->__setInitializer(null);
 $class = $entityPersister->getClassMetadata();
 $identifier = $classMetadata->getIdentifierValues($proxy);
 $original = $entityPersister->loadById($identifier);
 if ($original === null) {
 throw EntityNotFoundException::fromClassNameAndIdentifier($classMetadata->getName(), $this->identifierFlattener->flattenIdentifier($classMetadata, $identifier));
 }
 foreach ($class->getReflectionProperties() as $property) {
 if (!$class->hasField($property->name) && !$class->hasAssociation($property->name)) {
 continue;
 }
 $property->setAccessible(\true);
 $property->setValue($proxy, $property->getValue($original));
 }
 };
 }
}
