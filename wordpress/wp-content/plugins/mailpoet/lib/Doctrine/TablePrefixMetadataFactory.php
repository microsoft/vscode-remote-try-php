<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataInfo;

// Taken from Doctrine docs (see link bellow) but implemented in metadata factory instead of an event
// because we need to add prefix at runtime, not at metadata dump (which is included in builds).
// @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.5/cookbook/sql-table-prefixes.html
class TablePrefixMetadataFactory extends ClassMetadataFactory {
  // WordPress tables that are used by MailPoet via Doctrine
  const WP_TABLES = [
    'posts',
  ];

  /** @var string */
  private $prefix;

  /** @var string */
  private $wpDbPrefix;

  /** @var array */
  private $prefixedMap = [];

  public function __construct() {
    if (Env::$dbPrefix === null) {
      throw new \RuntimeException('DB table prefix not initialized');
    }
    $this->prefix = Env::$dbPrefix;
    $this->wpDbPrefix = Env::$wpDbPrefix;
    $this->setProxyClassNameResolver(new ProxyClassNameResolver());
  }

  /**
   * @return ClassMetadata<object>
   */
  public function getMetadataFor($className) {
    $classMetadata = parent::getMetadataFor($className);
    if (isset($this->prefixedMap[$classMetadata->getName()])) {
      return $classMetadata;
    }

    // prefix tables only after they are saved to cache so the prefix does not get included in cache
    // (getMetadataFor can call itself recursively but it saves to cache only after the recursive calls)
    $cacheKey = $this->getCacheKey($classMetadata->getName());
    $isCached = ($cache = $this->getCache()) ? $cache->hasItem($cacheKey) : false;
    if ($classMetadata instanceof ClassMetadata && $isCached) {
      $this->addPrefix($classMetadata);
      $this->prefixedMap[$classMetadata->getName()] = true;
    }
    return $classMetadata;
  }

  /**
   * @param ClassMetadata<object> $classMetadata
   */
  public function addPrefix(ClassMetadata $classMetadata) {
    if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
      $classMetadata->setPrimaryTable([
        'name' => $this->createPrefixedName($classMetadata->getTableName()),
      ]);
    }

    foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
      if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
        /** @var string $mappedTableName */
        $mappedTableName = $mapping['joinTable']['name'];
        $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->createPrefixedName($mappedTableName);
      }
    }
  }

  /**
   * MailPoet tables are prefixed by WP prefix + plugin prefix.
   * For entities for WP tables we use WP prefix only.
   */
  private function createPrefixedName(string $tableName): string {
    // Use WP prefix for WP tables
    if (in_array($tableName, self::WP_TABLES, true)) {
      return $this->wpDbPrefix . $tableName;
    }
    // Use WP + plugin prefix for MailPoet tables
    return $this->prefix . $tableName;
  }
}
