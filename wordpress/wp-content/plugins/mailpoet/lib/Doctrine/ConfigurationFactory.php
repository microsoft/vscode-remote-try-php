<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Annotations\AnnotationReaderProvider;
use MailPoetVendor\Doctrine\Common\Proxy\AbstractProxyFactory;
use MailPoetVendor\Doctrine\ORM\Configuration;
use MailPoetVendor\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use MailPoetVendor\Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

class ConfigurationFactory {
  const ENTITY_DIR = __DIR__ . '/../Entities';
  const METADATA_DIR = __DIR__ . '/../../generated/doctrine-metadata';
  const PROXY_DIR = __DIR__ . '/../../generated/doctrine-proxies';
  const PROXY_NAMESPACE = 'MailPoetDoctrineProxies';

  /** @var bool */
  private $isDevMode;

  /** @var AnnotationReaderProvider */
  private $annotationReaderProvider;

  public function __construct(
    AnnotationReaderProvider $annotationReaderProvider,
    $isDevMode = null
  ) {
    $this->isDevMode = $isDevMode === null ? WP_DEBUG : $isDevMode;
    $this->annotationReaderProvider = $annotationReaderProvider;
  }

  public function createConfiguration() {
    $configuration = new Configuration();
    $configuration->setNamingStrategy(new UnderscoreNamingStrategy(\CASE_LOWER, true));

    $this->configureMetadata($configuration);
    $this->configureProxies($configuration);
    $this->configureCache($configuration);
    return $configuration;
  }

  private function configureMetadata(Configuration $configuration) {
    $configuration->setClassMetadataFactoryName(TablePrefixMetadataFactory::class);

    // annotation reader exists only in dev environment, on production cache is pre-generated
    $annotationReader = $this->annotationReaderProvider->getAnnotationReader();
    $isReadOnly = !$annotationReader;
    $metadataStorage = new PSRMetadataCache(self::METADATA_DIR, $isReadOnly);
    $configuration->setMetadataCache($metadataStorage);

    if ($isReadOnly) {
      $configuration->setMetadataDriverImpl(new CacheOnlyMappingDriver($metadataStorage));
    } else {
      $configuration->setMetadataDriverImpl(new AnnotationDriver($annotationReader, [self::ENTITY_DIR]));
    }
  }

  private function configureProxies(Configuration $configuration) {
    $configuration->setProxyDir(self::PROXY_DIR);
    $configuration->setProxyNamespace(self::PROXY_NAMESPACE);
    $configuration->setAutoGenerateProxyClasses(
      $this->isDevMode
        ? AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS
        : AbstractProxyFactory::AUTOGENERATE_NEVER
    );
  }

  private function configureCache(Configuration $configuration) {
    $cache = new ArrayCache();
    $configuration->setQueryCacheImpl($cache);
    $configuration->setResultCacheImpl($cache);
  }
}
