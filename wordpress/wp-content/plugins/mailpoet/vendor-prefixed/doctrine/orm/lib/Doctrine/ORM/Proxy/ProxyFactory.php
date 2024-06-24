<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Proxy;
if (!defined('ABSPATH')) exit;
use Closure;
use MailPoetVendor\Doctrine\Common\Proxy\AbstractProxyFactory;
use MailPoetVendor\Doctrine\Common\Proxy\Proxy as CommonProxy;
use MailPoetVendor\Doctrine\Common\Proxy\ProxyDefinition;
use MailPoetVendor\Doctrine\Common\Proxy\ProxyGenerator;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\EntityNotFoundException;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
use MailPoetVendor\Doctrine\ORM\Proxy\Proxy as LegacyProxy;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use MailPoetVendor\Doctrine\ORM\Utility\IdentifierFlattener;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Proxy;
use ReflectionProperty;
use MailPoetVendor\Symfony\Component\VarExporter\ProxyHelper;
use MailPoetVendor\Symfony\Component\VarExporter\VarExporter;
use function array_flip;
use function str_replace;
use function strpos;
use function substr;
use function uksort;
class ProxyFactory extends AbstractProxyFactory
{
 private const PROXY_CLASS_TEMPLATE = <<<'EOPHP'
<?php
namespace <namespace>;
class <proxyShortClassName> extends \<className> implements \<baseProxyInterface>
{
 <useLazyGhostTrait>
 public bool $__isCloning = false;
 public function __construct(?\Closure $initializer = null)
 {
 self::createLazyGhost($initializer, <skippedProperties>, $this);
 }
 public function __isInitialized(): bool
 {
 return isset($this->lazyObjectState) && $this->isLazyObjectInitialized();
 }
 public function __clone()
 {
 $this->__isCloning = true;
 try {
 $this->__doClone();
 } finally {
 $this->__isCloning = false;
 }
 }
 public function __serialize(): array
 {
 <serializeImpl>
 }
}
EOPHP;
 private $em;
 private $uow;
 private $proxyNs;
 private $identifierFlattener;
 public function __construct(EntityManagerInterface $em, $proxyDir, $proxyNs, $autoGenerate = self::AUTOGENERATE_NEVER)
 {
 $proxyGenerator = new ProxyGenerator($proxyDir, $proxyNs);
 if ($em->getConfiguration()->isLazyGhostObjectEnabled()) {
 $proxyGenerator->setPlaceholder('baseProxyInterface', Proxy::class);
 $proxyGenerator->setPlaceholder('useLazyGhostTrait', Closure::fromCallable([$this, 'generateUseLazyGhostTrait']));
 $proxyGenerator->setPlaceholder('skippedProperties', Closure::fromCallable([$this, 'generateSkippedProperties']));
 $proxyGenerator->setPlaceholder('serializeImpl', Closure::fromCallable([$this, 'generateSerializeImpl']));
 $proxyGenerator->setProxyClassTemplate(self::PROXY_CLASS_TEMPLATE);
 } else {
 $proxyGenerator->setPlaceholder('baseProxyInterface', LegacyProxy::class);
 }
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
 if ($this->em->getConfiguration()->isLazyGhostObjectEnabled()) {
 $initializer = $this->createLazyInitializer($classMetadata, $entityPersister);
 $cloner = static function () : void {
 };
 } else {
 $initializer = $this->createInitializer($classMetadata, $entityPersister);
 $cloner = $this->createCloner($classMetadata, $entityPersister);
 }
 return new ProxyDefinition(ClassUtils::generateProxyClassName($className, $this->proxyNs), $classMetadata->getIdentifierFieldNames(), $classMetadata->getReflectionProperties(), $initializer, $cloner);
 }
 private function createInitializer(ClassMetadata $classMetadata, EntityPersister $entityPersister) : Closure
 {
 $wakeupProxy = $classMetadata->getReflectionClass()->hasMethod('__wakeup');
 return function (CommonProxy $proxy) use($entityPersister, $classMetadata, $wakeupProxy) : void {
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
 private function createLazyInitializer(ClassMetadata $classMetadata, EntityPersister $entityPersister) : Closure
 {
 return function (Proxy $proxy) use($entityPersister, $classMetadata) : void {
 $identifier = $classMetadata->getIdentifierValues($proxy);
 $entity = $entityPersister->loadById($identifier, $proxy->__isCloning ? null : $proxy);
 if ($entity === null) {
 throw EntityNotFoundException::fromClassNameAndIdentifier($classMetadata->getName(), $this->identifierFlattener->flattenIdentifier($classMetadata, $identifier));
 }
 if (!$proxy->__isCloning) {
 return;
 }
 $class = $entityPersister->getClassMetadata();
 foreach ($class->getReflectionProperties() as $property) {
 if (!$class->hasField($property->name) && !$class->hasAssociation($property->name)) {
 continue;
 }
 $property->setAccessible(\true);
 $property->setValue($proxy, $property->getValue($entity));
 }
 };
 }
 private function createCloner(ClassMetadata $classMetadata, EntityPersister $entityPersister) : Closure
 {
 return function (CommonProxy $proxy) use($entityPersister, $classMetadata) : void {
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
 private function generateUseLazyGhostTrait(ClassMetadata $class) : string
 {
 $code = ProxyHelper::generateLazyGhost($class->getReflectionClass());
 $code = substr($code, 7 + (int) strpos($code, "\n{"));
 $code = substr($code, 0, (int) strpos($code, "\n}"));
 $code = str_replace('LazyGhostTrait;', str_replace("\n ", "\n", 'LazyGhostTrait {
 initializeLazyObject as __load;
 setLazyObjectAsInitialized as public __setInitialized;
 isLazyObjectInitialized as private;
 createLazyGhost as private;
 resetLazyObject as private;
 __clone as private __doClone;
 }'), $code);
 return $code;
 }
 private function generateSkippedProperties(ClassMetadata $class) : string
 {
 $skippedProperties = ['__isCloning' => \true];
 $identifiers = array_flip($class->getIdentifierFieldNames());
 $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE;
 $reflector = $class->getReflectionClass();
 while ($reflector) {
 foreach ($reflector->getProperties($filter) as $property) {
 $name = $property->getName();
 if ($property->isStatic() || ($class->hasField($name) || $class->hasAssociation($name)) && !isset($identifiers[$name])) {
 continue;
 }
 $prefix = $property->isPrivate() ? "\x00" . $property->getDeclaringClass()->getName() . "\x00" : ($property->isProtected() ? "\x00*\x00" : '');
 $skippedProperties[$prefix . $name] = \true;
 }
 $filter = ReflectionProperty::IS_PRIVATE;
 $reflector = $reflector->getParentClass();
 }
 uksort($skippedProperties, 'strnatcmp');
 $code = VarExporter::export($skippedProperties);
 $code = str_replace(VarExporter::export($class->getName()), 'parent::class', $code);
 $code = str_replace("\n", "\n ", $code);
 return $code;
 }
 private function generateSerializeImpl(ClassMetadata $class) : string
 {
 $reflector = $class->getReflectionClass();
 $properties = $reflector->hasMethod('__serialize') ? 'parent::__serialize()' : '(array) $this';
 $code = '$properties = ' . $properties . ';
 unset($properties["\\0" . self::class . "\\0lazyObjectState"], $properties[\'__isCloning\']);
 ';
 if ($reflector->hasMethod('__serialize') || !$reflector->hasMethod('__sleep')) {
 return $code . 'return $properties;';
 }
 return $code . '$data = [];
 foreach (parent::__sleep() as $name) {
 $value = $properties[$k = $name] ?? $properties[$k = "\\0*\\0$name"] ?? $properties[$k = "\\0' . $reflector->getName() . '\\0$name"] ?? $k = null;
 if (null === $k) {
 trigger_error(sprintf(\'serialize(): "%s" returned as member variable from __sleep() but does not exist\', $name), \\E_USER_NOTICE);
 } else {
 $data[$k] = $value;
 }
 }
 return $data;';
 }
}
