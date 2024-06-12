<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Annotations\AnnotationReader;
use MailPoetVendor\Doctrine\Common\Annotations\CachedReader;
use MailPoetVendor\Doctrine\Common\Annotations\PsrCachedReader;
use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Doctrine\Common\Cache\ArrayCache;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use MailPoetVendor\Symfony\Component\Cache\Adapter\ArrayAdapter;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextFactory;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\XmlFileLoader;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\YamlFileLoader;
use MailPoetVendor\Symfony\Component\Validator\Validator\RecursiveValidator;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorTrait;
// Help opcache.preload discover always-needed symbols
\class_exists(TranslatorInterface::class);
\class_exists(LocaleAwareInterface::class);
\class_exists(TranslatorTrait::class);
class ValidatorBuilder
{
 private $initializers = [];
 private $loaders = [];
 private $xmlMappings = [];
 private $yamlMappings = [];
 private $methodMappings = [];
 private $annotationReader;
 private $enableAnnotationMapping = \false;
 private $metadataFactory;
 private $validatorFactory;
 private $mappingCache;
 private $translator;
 private $translationDomain;
 public function addObjectInitializer(ObjectInitializerInterface $initializer)
 {
 $this->initializers[] = $initializer;
 return $this;
 }
 public function addObjectInitializers(array $initializers)
 {
 $this->initializers = \array_merge($this->initializers, $initializers);
 return $this;
 }
 public function addXmlMapping(string $path)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->xmlMappings[] = $path;
 return $this;
 }
 public function addXmlMappings(array $paths)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->xmlMappings = \array_merge($this->xmlMappings, $paths);
 return $this;
 }
 public function addYamlMapping(string $path)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->yamlMappings[] = $path;
 return $this;
 }
 public function addYamlMappings(array $paths)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->yamlMappings = \array_merge($this->yamlMappings, $paths);
 return $this;
 }
 public function addMethodMapping(string $methodName)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->methodMappings[] = $methodName;
 return $this;
 }
 public function addMethodMappings(array $methodNames)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->methodMappings = \array_merge($this->methodMappings, $methodNames);
 return $this;
 }
 public function enableAnnotationMapping()
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot enable annotation mapping after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $skipDoctrineAnnotations = 1 > \func_num_args() ? \false : \func_get_arg(0);
 if (\false === $skipDoctrineAnnotations || null === $skipDoctrineAnnotations) {
 trigger_deprecation('symfony/validator', '5.2', 'Not passing true as first argument to "%s" is deprecated. Pass true and call "addDefaultDoctrineAnnotationReader()" if you want to enable annotation mapping with Doctrine Annotations.', __METHOD__);
 $this->addDefaultDoctrineAnnotationReader();
 } elseif ($skipDoctrineAnnotations instanceof Reader) {
 trigger_deprecation('symfony/validator', '5.2', 'Passing an instance of "%s" as first argument to "%s" is deprecated. Pass true instead and call setDoctrineAnnotationReader() if you want to enable annotation mapping with Doctrine Annotations.', \get_debug_type($skipDoctrineAnnotations), __METHOD__);
 $this->setDoctrineAnnotationReader($skipDoctrineAnnotations);
 } elseif (\true !== $skipDoctrineAnnotations) {
 throw new \TypeError(\sprintf('"%s": Argument 1 is expected to be a boolean, "%s" given.', __METHOD__, \get_debug_type($skipDoctrineAnnotations)));
 }
 $this->enableAnnotationMapping = \true;
 return $this;
 }
 public function disableAnnotationMapping()
 {
 $this->enableAnnotationMapping = \false;
 $this->annotationReader = null;
 return $this;
 }
 public function setDoctrineAnnotationReader(?Reader $reader) : self
 {
 $this->annotationReader = $reader;
 return $this;
 }
 public function addDefaultDoctrineAnnotationReader() : self
 {
 $this->annotationReader = $this->createAnnotationReader();
 return $this;
 }
 public function setMetadataFactory(MetadataFactoryInterface $metadataFactory)
 {
 if (\count($this->xmlMappings) > 0 || \count($this->yamlMappings) > 0 || \count($this->methodMappings) > 0 || $this->enableAnnotationMapping) {
 throw new ValidatorException('You cannot set a custom metadata factory after adding custom mappings. You should do either of both.');
 }
 $this->metadataFactory = $metadataFactory;
 return $this;
 }
 public function setMappingCache(CacheItemPoolInterface $cache)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot set a custom mapping cache after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->mappingCache = $cache;
 return $this;
 }
 public function setConstraintValidatorFactory(ConstraintValidatorFactoryInterface $validatorFactory)
 {
 $this->validatorFactory = $validatorFactory;
 return $this;
 }
 public function setTranslator(TranslatorInterface $translator)
 {
 $this->translator = $translator;
 return $this;
 }
 public function setTranslationDomain(?string $translationDomain)
 {
 $this->translationDomain = $translationDomain;
 return $this;
 }
 public function addLoader(LoaderInterface $loader)
 {
 $this->loaders[] = $loader;
 return $this;
 }
 public function getLoaders()
 {
 $loaders = [];
 foreach ($this->xmlMappings as $xmlMapping) {
 $loaders[] = new XmlFileLoader($xmlMapping);
 }
 foreach ($this->yamlMappings as $yamlMappings) {
 $loaders[] = new YamlFileLoader($yamlMappings);
 }
 foreach ($this->methodMappings as $methodName) {
 $loaders[] = new StaticMethodLoader($methodName);
 }
 if ($this->enableAnnotationMapping) {
 $loaders[] = new AnnotationLoader($this->annotationReader);
 }
 return \array_merge($loaders, $this->loaders);
 }
 public function getValidator()
 {
 $metadataFactory = $this->metadataFactory;
 if (!$metadataFactory) {
 $loaders = $this->getLoaders();
 $loader = null;
 if (\count($loaders) > 1) {
 $loader = new LoaderChain($loaders);
 } elseif (1 === \count($loaders)) {
 $loader = $loaders[0];
 }
 $metadataFactory = new LazyLoadingMetadataFactory($loader, $this->mappingCache);
 }
 $validatorFactory = $this->validatorFactory ?? new ConstraintValidatorFactory();
 $translator = $this->translator;
 if (null === $translator) {
 $translator = new class implements TranslatorInterface, LocaleAwareInterface
 {
 use TranslatorTrait;
 };
 // Force the locale to be 'en' when no translator is provided rather than relying on the Intl default locale
 // This avoids depending on Intl or the stub implementation being available. It also ensures that Symfony
 // validation messages are pluralized properly even when the default locale gets changed because they are in
 // English.
 $translator->setLocale('en');
 }
 $contextFactory = new ExecutionContextFactory($translator, $this->translationDomain);
 return new RecursiveValidator($contextFactory, $metadataFactory, $validatorFactory, $this->initializers);
 }
 private function createAnnotationReader() : Reader
 {
 if (!\class_exists(AnnotationReader::class)) {
 throw new LogicException('Enabling annotation based constraint mapping requires the packages doctrine/annotations and symfony/cache to be installed.');
 }
 if (\class_exists(ArrayAdapter::class)) {
 return new PsrCachedReader(new AnnotationReader(), new ArrayAdapter());
 }
 if (\class_exists(CachedReader::class) && \class_exists(ArrayCache::class)) {
 trigger_deprecation('symfony/validator', '5.4', 'Enabling annotation based constraint mapping without having symfony/cache installed is deprecated.');
 return new CachedReader(new AnnotationReader(), new ArrayCache());
 }
 throw new LogicException('Enabling annotation based constraint mapping requires the packages doctrine/annotations and symfony/cache to be installed.');
 }
}
