<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use BadMethodCallException;
use MailPoetVendor\Doctrine\Common\Annotations\AnnotationReader;
use MailPoetVendor\Doctrine\Common\Annotations\CachedReader;
use MailPoetVendor\Doctrine\Common\Annotations\SimpleAnnotationReader;
use MailPoetVendor\Doctrine\Common\Cache\ArrayCache;
use MailPoetVendor\Doctrine\Common\Cache\Cache as CacheDriver;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\Common\Persistence\PersistentObject;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Cache\CacheConfiguration;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\CacheException;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\MetadataCacheNotConfigured;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\MetadataCacheUsesNonPersistentCache;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\QueryCacheNotConfigured;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\QueryCacheUsesNonPersistentCache;
use MailPoetVendor\Doctrine\ORM\Exception\InvalidEntityRepository;
use MailPoetVendor\Doctrine\ORM\Exception\NamedNativeQueryNotFound;
use MailPoetVendor\Doctrine\ORM\Exception\NamedQueryNotFound;
use MailPoetVendor\Doctrine\ORM\Exception\NotSupported;
use MailPoetVendor\Doctrine\ORM\Exception\ProxyClassesAlwaysRegenerating;
use MailPoetVendor\Doctrine\ORM\Exception\UnknownEntityNamespace;
use MailPoetVendor\Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory;
use MailPoetVendor\Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use MailPoetVendor\Doctrine\ORM\Mapping\DefaultNamingStrategy;
use MailPoetVendor\Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use MailPoetVendor\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use MailPoetVendor\Doctrine\ORM\Mapping\EntityListenerResolver;
use MailPoetVendor\Doctrine\ORM\Mapping\NamingStrategy;
use MailPoetVendor\Doctrine\ORM\Mapping\QuoteStrategy;
use MailPoetVendor\Doctrine\ORM\Mapping\TypedFieldMapper;
use MailPoetVendor\Doctrine\ORM\Proxy\ProxyFactory;
use MailPoetVendor\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use MailPoetVendor\Doctrine\ORM\Query\Filter\SQLFilter;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\ORM\Repository\DefaultRepositoryFactory;
use MailPoetVendor\Doctrine\ORM\Repository\RepositoryFactory;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MailPoetVendor\Doctrine\Persistence\ObjectRepository;
use MailPoetVendor\Doctrine\Persistence\Reflection\RuntimeReflectionProperty;
use LogicException;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use MailPoetVendor\Symfony\Component\VarExporter\LazyGhostTrait;
use function class_exists;
use function is_a;
use function method_exists;
use function sprintf;
use function strtolower;
use function trait_exists;
use function trim;
class Configuration extends \MailPoetVendor\Doctrine\DBAL\Configuration
{
 protected $_attributes = [];
 public function setProxyDir($dir)
 {
 $this->_attributes['proxyDir'] = $dir;
 }
 public function getProxyDir()
 {
 return $this->_attributes['proxyDir'] ?? null;
 }
 public function getAutoGenerateProxyClasses()
 {
 return $this->_attributes['autoGenerateProxyClasses'] ?? ProxyFactory::AUTOGENERATE_ALWAYS;
 }
 public function setAutoGenerateProxyClasses($autoGenerate)
 {
 $this->_attributes['autoGenerateProxyClasses'] = (int) $autoGenerate;
 }
 public function getProxyNamespace()
 {
 return $this->_attributes['proxyNamespace'] ?? null;
 }
 public function setProxyNamespace($ns)
 {
 $this->_attributes['proxyNamespace'] = $ns;
 }
 public function setMetadataDriverImpl(MappingDriver $driverImpl)
 {
 $this->_attributes['metadataDriverImpl'] = $driverImpl;
 }
 public function newDefaultAnnotationDriver($paths = [], $useSimpleAnnotationReader = \true)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9443', '%s is deprecated, call %s::createDefaultAnnotationDriver() instead.', __METHOD__, ORMSetup::class);
 if (!class_exists(AnnotationReader::class)) {
 throw new LogicException('The annotation metadata driver cannot be enabled because the "doctrine/annotations" library' . ' is not installed. Please run "composer require doctrine/annotations" or choose a different' . ' metadata driver.');
 }
 if ($useSimpleAnnotationReader) {
 if (!class_exists(SimpleAnnotationReader::class)) {
 throw new BadMethodCallException('SimpleAnnotationReader has been removed in doctrine/annotations 2.' . ' Downgrade to version 1 or set $useSimpleAnnotationReader to false.');
 }
 // Register the ORM Annotations in the AnnotationRegistry
 $reader = new SimpleAnnotationReader();
 $reader->addNamespace('MailPoetVendor\\Doctrine\\ORM\\Mapping');
 } else {
 $reader = new AnnotationReader();
 }
 if (class_exists(ArrayCache::class) && class_exists(CachedReader::class)) {
 $reader = new CachedReader($reader, new ArrayCache());
 }
 return new AnnotationDriver($reader, (array) $paths);
 }
 public function addEntityNamespace($alias, $namespace)
 {
 if (class_exists(PersistentObject::class)) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8818', 'Short namespace aliases such as "%s" are deprecated and will be removed in Doctrine ORM 3.0.', $alias);
 } else {
 throw NotSupported::createForPersistence3(sprintf('Using short namespace alias "%s" by calling %s', $alias, __METHOD__));
 }
 $this->_attributes['entityNamespaces'][$alias] = $namespace;
 }
 public function getEntityNamespace($entityNamespaceAlias)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8818', 'Entity short namespace aliases such as "%s" are deprecated, use ::class constant instead.', $entityNamespaceAlias);
 if (!isset($this->_attributes['entityNamespaces'][$entityNamespaceAlias])) {
 throw UnknownEntityNamespace::fromNamespaceAlias($entityNamespaceAlias);
 }
 return trim($this->_attributes['entityNamespaces'][$entityNamespaceAlias], '\\');
 }
 public function setEntityNamespaces(array $entityNamespaces)
 {
 $this->_attributes['entityNamespaces'] = $entityNamespaces;
 }
 public function getEntityNamespaces()
 {
 return $this->_attributes['entityNamespaces'];
 }
 public function getMetadataDriverImpl()
 {
 return $this->_attributes['metadataDriverImpl'] ?? null;
 }
 public function getResultCache() : ?CacheItemPoolInterface
 {
 // Compatibility with DBAL 2
 if (!method_exists(parent::class, 'getResultCache')) {
 $cacheImpl = $this->getResultCacheImpl();
 return $cacheImpl ? CacheAdapter::wrap($cacheImpl) : null;
 }
 return parent::getResultCache();
 }
 public function setResultCache(CacheItemPoolInterface $cache) : void
 {
 // Compatibility with DBAL 2
 if (!method_exists(parent::class, 'setResultCache')) {
 $this->setResultCacheImpl(DoctrineProvider::wrap($cache));
 return;
 }
 parent::setResultCache($cache);
 }
 public function getQueryCacheImpl()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9002', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getQueryCache() instead.', __METHOD__);
 return $this->_attributes['queryCacheImpl'] ?? null;
 }
 public function setQueryCacheImpl(CacheDriver $cacheImpl)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9002', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use setQueryCache() instead.', __METHOD__);
 $this->_attributes['queryCache'] = CacheAdapter::wrap($cacheImpl);
 $this->_attributes['queryCacheImpl'] = $cacheImpl;
 }
 public function getQueryCache() : ?CacheItemPoolInterface
 {
 return $this->_attributes['queryCache'] ?? null;
 }
 public function setQueryCache(CacheItemPoolInterface $cache) : void
 {
 $this->_attributes['queryCache'] = $cache;
 $this->_attributes['queryCacheImpl'] = DoctrineProvider::wrap($cache);
 }
 public function getHydrationCacheImpl()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9002', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getHydrationCache() instead.', __METHOD__);
 return $this->_attributes['hydrationCacheImpl'] ?? null;
 }
 public function setHydrationCacheImpl(CacheDriver $cacheImpl)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9002', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use setHydrationCache() instead.', __METHOD__);
 $this->_attributes['hydrationCache'] = CacheAdapter::wrap($cacheImpl);
 $this->_attributes['hydrationCacheImpl'] = $cacheImpl;
 }
 public function getHydrationCache() : ?CacheItemPoolInterface
 {
 return $this->_attributes['hydrationCache'] ?? null;
 }
 public function setHydrationCache(CacheItemPoolInterface $cache) : void
 {
 $this->_attributes['hydrationCache'] = $cache;
 $this->_attributes['hydrationCacheImpl'] = DoctrineProvider::wrap($cache);
 }
 public function getMetadataCacheImpl()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8650', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getMetadataCache() instead.', __METHOD__);
 if (isset($this->_attributes['metadataCacheImpl'])) {
 return $this->_attributes['metadataCacheImpl'];
 }
 return isset($this->_attributes['metadataCache']) ? DoctrineProvider::wrap($this->_attributes['metadataCache']) : null;
 }
 public function setMetadataCacheImpl(CacheDriver $cacheImpl)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8650', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use setMetadataCache() instead.', __METHOD__);
 $this->_attributes['metadataCacheImpl'] = $cacheImpl;
 $this->_attributes['metadataCache'] = CacheAdapter::wrap($cacheImpl);
 }
 public function getMetadataCache() : ?CacheItemPoolInterface
 {
 return $this->_attributes['metadataCache'] ?? null;
 }
 public function setMetadataCache(CacheItemPoolInterface $cache) : void
 {
 $this->_attributes['metadataCache'] = $cache;
 $this->_attributes['metadataCacheImpl'] = DoctrineProvider::wrap($cache);
 }
 public function addNamedQuery($name, $dql)
 {
 $this->_attributes['namedQueries'][$name] = $dql;
 }
 public function getNamedQuery($name)
 {
 if (!isset($this->_attributes['namedQueries'][$name])) {
 throw NamedQueryNotFound::fromName($name);
 }
 return $this->_attributes['namedQueries'][$name];
 }
 public function addNamedNativeQuery($name, $sql, Query\ResultSetMapping $rsm)
 {
 $this->_attributes['namedNativeQueries'][$name] = [$sql, $rsm];
 }
 public function getNamedNativeQuery($name)
 {
 if (!isset($this->_attributes['namedNativeQueries'][$name])) {
 throw NamedNativeQueryNotFound::fromName($name);
 }
 return $this->_attributes['namedNativeQueries'][$name];
 }
 public function ensureProductionSettings()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/orm', 'https://github.com/doctrine/orm/pull/9074', '%s is deprecated', __METHOD__);
 $queryCacheImpl = $this->getQueryCacheImpl();
 if (!$queryCacheImpl) {
 throw QueryCacheNotConfigured::create();
 }
 if ($queryCacheImpl instanceof ArrayCache) {
 throw QueryCacheUsesNonPersistentCache::fromDriver($queryCacheImpl);
 }
 if ($this->getAutoGenerateProxyClasses() !== ProxyFactory::AUTOGENERATE_NEVER) {
 throw ProxyClassesAlwaysRegenerating::create();
 }
 if (!$this->getMetadataCache()) {
 throw MetadataCacheNotConfigured::create();
 }
 $metadataCacheImpl = $this->getMetadataCacheImpl();
 if ($metadataCacheImpl instanceof ArrayCache) {
 throw MetadataCacheUsesNonPersistentCache::fromDriver($metadataCacheImpl);
 }
 }
 public function addCustomStringFunction($name, $className)
 {
 $this->_attributes['customStringFunctions'][strtolower($name)] = $className;
 }
 public function getCustomStringFunction($name)
 {
 $name = strtolower($name);
 return $this->_attributes['customStringFunctions'][$name] ?? null;
 }
 public function setCustomStringFunctions(array $functions)
 {
 foreach ($functions as $name => $className) {
 $this->addCustomStringFunction($name, $className);
 }
 }
 public function addCustomNumericFunction($name, $className)
 {
 $this->_attributes['customNumericFunctions'][strtolower($name)] = $className;
 }
 public function getCustomNumericFunction($name)
 {
 $name = strtolower($name);
 return $this->_attributes['customNumericFunctions'][$name] ?? null;
 }
 public function setCustomNumericFunctions(array $functions)
 {
 foreach ($functions as $name => $className) {
 $this->addCustomNumericFunction($name, $className);
 }
 }
 public function addCustomDatetimeFunction($name, $className)
 {
 $this->_attributes['customDatetimeFunctions'][strtolower($name)] = $className;
 }
 public function getCustomDatetimeFunction($name)
 {
 $name = strtolower($name);
 return $this->_attributes['customDatetimeFunctions'][$name] ?? null;
 }
 public function setCustomDatetimeFunctions(array $functions)
 {
 foreach ($functions as $name => $className) {
 $this->addCustomDatetimeFunction($name, $className);
 }
 }
 public function setTypedFieldMapper(?TypedFieldMapper $typedFieldMapper) : void
 {
 $this->_attributes['typedFieldMapper'] = $typedFieldMapper;
 }
 public function getTypedFieldMapper() : ?TypedFieldMapper
 {
 return $this->_attributes['typedFieldMapper'] ?? null;
 }
 public function setCustomHydrationModes($modes)
 {
 $this->_attributes['customHydrationModes'] = [];
 foreach ($modes as $modeName => $hydrator) {
 $this->addCustomHydrationMode($modeName, $hydrator);
 }
 }
 public function getCustomHydrationMode($modeName)
 {
 return $this->_attributes['customHydrationModes'][$modeName] ?? null;
 }
 public function addCustomHydrationMode($modeName, $hydrator)
 {
 $this->_attributes['customHydrationModes'][$modeName] = $hydrator;
 }
 public function setClassMetadataFactoryName($cmfName)
 {
 $this->_attributes['classMetadataFactoryName'] = $cmfName;
 }
 public function getClassMetadataFactoryName()
 {
 if (!isset($this->_attributes['classMetadataFactoryName'])) {
 $this->_attributes['classMetadataFactoryName'] = ClassMetadataFactory::class;
 }
 return $this->_attributes['classMetadataFactoryName'];
 }
 public function addFilter($name, $className)
 {
 $this->_attributes['filters'][$name] = $className;
 }
 public function getFilterClassName($name)
 {
 return $this->_attributes['filters'][$name] ?? null;
 }
 public function setDefaultRepositoryClassName($className)
 {
 if (!class_exists($className) || !is_a($className, ObjectRepository::class, \true)) {
 throw InvalidEntityRepository::fromClassName($className);
 }
 if (!is_a($className, EntityRepository::class, \true)) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9533', 'Configuring %s as default repository class is deprecated because it does not extend %s.', $className, EntityRepository::class);
 }
 $this->_attributes['defaultRepositoryClassName'] = $className;
 }
 public function getDefaultRepositoryClassName()
 {
 return $this->_attributes['defaultRepositoryClassName'] ?? EntityRepository::class;
 }
 public function setNamingStrategy(NamingStrategy $namingStrategy)
 {
 $this->_attributes['namingStrategy'] = $namingStrategy;
 }
 public function getNamingStrategy()
 {
 if (!isset($this->_attributes['namingStrategy'])) {
 $this->_attributes['namingStrategy'] = new DefaultNamingStrategy();
 }
 return $this->_attributes['namingStrategy'];
 }
 public function setQuoteStrategy(QuoteStrategy $quoteStrategy)
 {
 $this->_attributes['quoteStrategy'] = $quoteStrategy;
 }
 public function getQuoteStrategy()
 {
 if (!isset($this->_attributes['quoteStrategy'])) {
 $this->_attributes['quoteStrategy'] = new DefaultQuoteStrategy();
 }
 return $this->_attributes['quoteStrategy'];
 }
 public function setEntityListenerResolver(EntityListenerResolver $resolver)
 {
 $this->_attributes['entityListenerResolver'] = $resolver;
 }
 public function getEntityListenerResolver()
 {
 if (!isset($this->_attributes['entityListenerResolver'])) {
 $this->_attributes['entityListenerResolver'] = new DefaultEntityListenerResolver();
 }
 return $this->_attributes['entityListenerResolver'];
 }
 public function setRepositoryFactory(RepositoryFactory $repositoryFactory)
 {
 $this->_attributes['repositoryFactory'] = $repositoryFactory;
 }
 public function getRepositoryFactory()
 {
 return $this->_attributes['repositoryFactory'] ?? new DefaultRepositoryFactory();
 }
 public function isSecondLevelCacheEnabled()
 {
 return $this->_attributes['isSecondLevelCacheEnabled'] ?? \false;
 }
 public function setSecondLevelCacheEnabled($flag = \true)
 {
 $this->_attributes['isSecondLevelCacheEnabled'] = (bool) $flag;
 }
 public function setSecondLevelCacheConfiguration(CacheConfiguration $cacheConfig)
 {
 $this->_attributes['secondLevelCacheConfiguration'] = $cacheConfig;
 }
 public function getSecondLevelCacheConfiguration()
 {
 if (!isset($this->_attributes['secondLevelCacheConfiguration']) && $this->isSecondLevelCacheEnabled()) {
 $this->_attributes['secondLevelCacheConfiguration'] = new CacheConfiguration();
 }
 return $this->_attributes['secondLevelCacheConfiguration'] ?? null;
 }
 public function getDefaultQueryHints()
 {
 return $this->_attributes['defaultQueryHints'] ?? [];
 }
 public function setDefaultQueryHints(array $defaultQueryHints)
 {
 $this->_attributes['defaultQueryHints'] = $defaultQueryHints;
 }
 public function getDefaultQueryHint($name)
 {
 return $this->_attributes['defaultQueryHints'][$name] ?? \false;
 }
 public function setDefaultQueryHint($name, $value)
 {
 $this->_attributes['defaultQueryHints'][$name] = $value;
 }
 public function getSchemaIgnoreClasses() : array
 {
 return $this->_attributes['schemaIgnoreClasses'] ?? [];
 }
 public function setSchemaIgnoreClasses(array $schemaIgnoreClasses) : void
 {
 $this->_attributes['schemaIgnoreClasses'] = $schemaIgnoreClasses;
 }
 public function isLazyGhostObjectEnabled() : bool
 {
 return $this->_attributes['isLazyGhostObjectEnabled'] ?? \false;
 }
 public function setLazyGhostObjectEnabled(bool $flag) : void
 {
 if ($flag && !trait_exists(LazyGhostTrait::class)) {
 throw new LogicException('Lazy ghost objects cannot be enabled because the "symfony/var-exporter" library' . ' version 6.2 or higher is not installed. Please run "composer require symfony/var-exporter:^6.2".');
 }
 if ($flag && !class_exists(RuntimeReflectionProperty::class)) {
 throw new LogicException('Lazy ghost objects cannot be enabled because the "doctrine/persistence" library' . ' version 3.1 or higher is not installed. Please run "composer update doctrine/persistence".');
 }
 $this->_attributes['isLazyGhostObjectEnabled'] = $flag;
 }
}
