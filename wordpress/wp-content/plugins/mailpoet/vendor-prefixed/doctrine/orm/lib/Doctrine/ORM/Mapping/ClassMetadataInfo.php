<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use BadMethodCallException;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\Instantiator\Instantiator;
use MailPoetVendor\Doctrine\Instantiator\InstantiatorInterface;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\NonCacheableEntityAssociation;
use MailPoetVendor\Doctrine\ORM\EntityRepository;
use MailPoetVendor\Doctrine\ORM\Id\AbstractIdGenerator;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Mapping\ReflectionService;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;
use function array_diff;
use function array_flip;
use function array_intersect;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_values;
use function assert;
use function class_exists;
use function count;
use function enum_exists;
use function explode;
use function gettype;
use function in_array;
use function interface_exists;
use function is_array;
use function is_subclass_of;
use function ltrim;
use function method_exists;
use function spl_object_id;
use function str_replace;
use function strpos;
use function strtolower;
use function trait_exists;
use function trim;
use const PHP_VERSION_ID;
class ClassMetadataInfo implements ClassMetadata
{
 public const INHERITANCE_TYPE_NONE = 1;
 public const INHERITANCE_TYPE_JOINED = 2;
 public const INHERITANCE_TYPE_SINGLE_TABLE = 3;
 public const INHERITANCE_TYPE_TABLE_PER_CLASS = 4;
 public const GENERATOR_TYPE_AUTO = 1;
 public const GENERATOR_TYPE_SEQUENCE = 2;
 public const GENERATOR_TYPE_TABLE = 3;
 public const GENERATOR_TYPE_IDENTITY = 4;
 public const GENERATOR_TYPE_NONE = 5;
 public const GENERATOR_TYPE_UUID = 6;
 public const GENERATOR_TYPE_CUSTOM = 7;
 public const CHANGETRACKING_DEFERRED_IMPLICIT = 1;
 public const CHANGETRACKING_DEFERRED_EXPLICIT = 2;
 public const CHANGETRACKING_NOTIFY = 3;
 public const FETCH_LAZY = 2;
 public const FETCH_EAGER = 3;
 public const FETCH_EXTRA_LAZY = 4;
 public const ONE_TO_ONE = 1;
 public const MANY_TO_ONE = 2;
 public const ONE_TO_MANY = 4;
 public const MANY_TO_MANY = 8;
 public const TO_ONE = 3;
 public const TO_MANY = 12;
 public const CACHE_USAGE_READ_ONLY = 1;
 public const CACHE_USAGE_NONSTRICT_READ_WRITE = 2;
 public const CACHE_USAGE_READ_WRITE = 3;
 public const GENERATED_NEVER = 0;
 public const GENERATED_INSERT = 1;
 public const GENERATED_ALWAYS = 2;
 public $name;
 public $namespace;
 public $rootEntityName;
 public $customGeneratorDefinition;
 public $customRepositoryClassName;
 public $isMappedSuperclass = \false;
 public $isEmbeddedClass = \false;
 public $parentClasses = [];
 public $subClasses = [];
 public $embeddedClasses = [];
 public $namedQueries = [];
 public $namedNativeQueries = [];
 public $sqlResultSetMappings = [];
 public $identifier = [];
 public $inheritanceType = self::INHERITANCE_TYPE_NONE;
 public $generatorType = self::GENERATOR_TYPE_NONE;
 public $fieldMappings = [];
 public $fieldNames = [];
 public $columnNames = [];
 public $discriminatorValue;
 public $discriminatorMap = [];
 public $discriminatorColumn;
 public $table;
 public $lifecycleCallbacks = [];
 public $entityListeners = [];
 public $associationMappings = [];
 public $isIdentifierComposite = \false;
 public $containsForeignIdentifier = \false;
 public $idGenerator;
 public $sequenceGeneratorDefinition;
 public $tableGeneratorDefinition;
 public $changeTrackingPolicy = self::CHANGETRACKING_DEFERRED_IMPLICIT;
 public $requiresFetchAfterChange = \false;
 public $isVersioned = \false;
 public $versionField;
 public $cache;
 public $reflClass;
 public $isReadOnly = \false;
 protected $namingStrategy;
 public $reflFields = [];
 private $instantiator;
 public function __construct($entityName, ?NamingStrategy $namingStrategy = null)
 {
 $this->name = $entityName;
 $this->rootEntityName = $entityName;
 $this->namingStrategy = $namingStrategy ?: new DefaultNamingStrategy();
 $this->instantiator = new Instantiator();
 }
 public function getReflectionProperties()
 {
 return $this->reflFields;
 }
 public function getReflectionProperty($name)
 {
 return $this->reflFields[$name];
 }
 public function getSingleIdReflectionProperty()
 {
 if ($this->isIdentifierComposite) {
 throw new BadMethodCallException('Class ' . $this->name . ' has a composite identifier.');
 }
 return $this->reflFields[$this->identifier[0]];
 }
 public function getIdentifierValues($entity)
 {
 if ($this->isIdentifierComposite) {
 $id = [];
 foreach ($this->identifier as $idField) {
 $value = $this->reflFields[$idField]->getValue($entity);
 if ($value !== null) {
 $id[$idField] = $value;
 }
 }
 return $id;
 }
 $id = $this->identifier[0];
 $value = $this->reflFields[$id]->getValue($entity);
 if ($value === null) {
 return [];
 }
 return [$id => $value];
 }
 public function setIdentifierValues($entity, array $id)
 {
 foreach ($id as $idField => $idValue) {
 $this->reflFields[$idField]->setValue($entity, $idValue);
 }
 }
 public function setFieldValue($entity, $field, $value)
 {
 $this->reflFields[$field]->setValue($entity, $value);
 }
 public function getFieldValue($entity, $field)
 {
 return $this->reflFields[$field]->getValue($entity);
 }
 public function __toString()
 {
 return self::class . '@' . spl_object_id($this);
 }
 public function __sleep()
 {
 // This metadata is always serialized/cached.
 $serialized = [
 'associationMappings',
 'columnNames',
 //TODO: 3.0 Remove this. Can use fieldMappings[$fieldName]['columnName']
 'fieldMappings',
 'fieldNames',
 'embeddedClasses',
 'identifier',
 'isIdentifierComposite',
 // TODO: REMOVE
 'name',
 'namespace',
 // TODO: REMOVE
 'table',
 'rootEntityName',
 'idGenerator',
 ];
 // The rest of the metadata is only serialized if necessary.
 if ($this->changeTrackingPolicy !== self::CHANGETRACKING_DEFERRED_IMPLICIT) {
 $serialized[] = 'changeTrackingPolicy';
 }
 if ($this->customRepositoryClassName) {
 $serialized[] = 'customRepositoryClassName';
 }
 if ($this->inheritanceType !== self::INHERITANCE_TYPE_NONE) {
 $serialized[] = 'inheritanceType';
 $serialized[] = 'discriminatorColumn';
 $serialized[] = 'discriminatorValue';
 $serialized[] = 'discriminatorMap';
 $serialized[] = 'parentClasses';
 $serialized[] = 'subClasses';
 }
 if ($this->generatorType !== self::GENERATOR_TYPE_NONE) {
 $serialized[] = 'generatorType';
 if ($this->generatorType === self::GENERATOR_TYPE_SEQUENCE) {
 $serialized[] = 'sequenceGeneratorDefinition';
 }
 }
 if ($this->isMappedSuperclass) {
 $serialized[] = 'isMappedSuperclass';
 }
 if ($this->isEmbeddedClass) {
 $serialized[] = 'isEmbeddedClass';
 }
 if ($this->containsForeignIdentifier) {
 $serialized[] = 'containsForeignIdentifier';
 }
 if ($this->isVersioned) {
 $serialized[] = 'isVersioned';
 $serialized[] = 'versionField';
 }
 if ($this->lifecycleCallbacks) {
 $serialized[] = 'lifecycleCallbacks';
 }
 if ($this->entityListeners) {
 $serialized[] = 'entityListeners';
 }
 if ($this->namedQueries) {
 $serialized[] = 'namedQueries';
 }
 if ($this->namedNativeQueries) {
 $serialized[] = 'namedNativeQueries';
 }
 if ($this->sqlResultSetMappings) {
 $serialized[] = 'sqlResultSetMappings';
 }
 if ($this->isReadOnly) {
 $serialized[] = 'isReadOnly';
 }
 if ($this->customGeneratorDefinition) {
 $serialized[] = 'customGeneratorDefinition';
 }
 if ($this->cache) {
 $serialized[] = 'cache';
 }
 if ($this->requiresFetchAfterChange) {
 $serialized[] = 'requiresFetchAfterChange';
 }
 return $serialized;
 }
 public function newInstance()
 {
 return $this->instantiator->instantiate($this->name);
 }
 public function wakeupReflection($reflService)
 {
 // Restore ReflectionClass and properties
 $this->reflClass = $reflService->getClass($this->name);
 $this->instantiator = $this->instantiator ?: new Instantiator();
 $parentReflFields = [];
 foreach ($this->embeddedClasses as $property => $embeddedClass) {
 if (isset($embeddedClass['declaredField'])) {
 $childProperty = $this->getAccessibleProperty($reflService, $this->embeddedClasses[$embeddedClass['declaredField']]['class'], $embeddedClass['originalField']);
 assert($childProperty !== null);
 $parentReflFields[$property] = new ReflectionEmbeddedProperty($parentReflFields[$embeddedClass['declaredField']], $childProperty, $this->embeddedClasses[$embeddedClass['declaredField']]['class']);
 continue;
 }
 $fieldRefl = $this->getAccessibleProperty($reflService, $embeddedClass['declared'] ?? $this->name, $property);
 $parentReflFields[$property] = $fieldRefl;
 $this->reflFields[$property] = $fieldRefl;
 }
 foreach ($this->fieldMappings as $field => $mapping) {
 if (isset($mapping['declaredField']) && isset($parentReflFields[$mapping['declaredField']])) {
 $childProperty = $this->getAccessibleProperty($reflService, $mapping['originalClass'], $mapping['originalField']);
 assert($childProperty !== null);
 if (isset($mapping['enumType'])) {
 $childProperty = new ReflectionEnumProperty($childProperty, $mapping['enumType']);
 }
 $this->reflFields[$field] = new ReflectionEmbeddedProperty($parentReflFields[$mapping['declaredField']], $childProperty, $mapping['originalClass']);
 continue;
 }
 $this->reflFields[$field] = isset($mapping['declared']) ? $this->getAccessibleProperty($reflService, $mapping['declared'], $field) : $this->getAccessibleProperty($reflService, $this->name, $field);
 if (isset($mapping['enumType']) && $this->reflFields[$field] !== null) {
 $this->reflFields[$field] = new ReflectionEnumProperty($this->reflFields[$field], $mapping['enumType']);
 }
 }
 foreach ($this->associationMappings as $field => $mapping) {
 $this->reflFields[$field] = isset($mapping['declared']) ? $this->getAccessibleProperty($reflService, $mapping['declared'], $field) : $this->getAccessibleProperty($reflService, $this->name, $field);
 }
 }
 public function initializeReflection($reflService)
 {
 $this->reflClass = $reflService->getClass($this->name);
 $this->namespace = $reflService->getClassNamespace($this->name);
 if ($this->reflClass) {
 $this->name = $this->rootEntityName = $this->reflClass->getName();
 }
 $this->table['name'] = $this->namingStrategy->classToTableName($this->name);
 }
 public function validateIdentifier()
 {
 if ($this->isMappedSuperclass || $this->isEmbeddedClass) {
 return;
 }
 // Verify & complete identifier mapping
 if (!$this->identifier) {
 throw MappingException::identifierRequired($this->name);
 }
 if ($this->usesIdGenerator() && $this->isIdentifierComposite) {
 throw MappingException::compositeKeyAssignedIdGeneratorRequired($this->name);
 }
 }
 public function validateAssociations()
 {
 foreach ($this->associationMappings as $mapping) {
 if (!class_exists($mapping['targetEntity']) && !interface_exists($mapping['targetEntity']) && !trait_exists($mapping['targetEntity'])) {
 throw MappingException::invalidTargetEntityClass($mapping['targetEntity'], $this->name, $mapping['fieldName']);
 }
 }
 }
 public function validateLifecycleCallbacks($reflService)
 {
 foreach ($this->lifecycleCallbacks as $callbacks) {
 foreach ($callbacks as $callbackFuncName) {
 if (!$reflService->hasPublicMethod($this->name, $callbackFuncName)) {
 throw MappingException::lifecycleCallbackMethodNotFound($this->name, $callbackFuncName);
 }
 }
 }
 }
 public function getReflectionClass()
 {
 return $this->reflClass;
 }
 public function enableCache(array $cache)
 {
 if (!isset($cache['usage'])) {
 $cache['usage'] = self::CACHE_USAGE_READ_ONLY;
 }
 if (!isset($cache['region'])) {
 $cache['region'] = strtolower(str_replace('\\', '_', $this->rootEntityName));
 }
 $this->cache = $cache;
 }
 public function enableAssociationCache($fieldName, array $cache)
 {
 $this->associationMappings[$fieldName]['cache'] = $this->getAssociationCacheDefaults($fieldName, $cache);
 }
 public function getAssociationCacheDefaults($fieldName, array $cache)
 {
 if (!isset($cache['usage'])) {
 $cache['usage'] = $this->cache['usage'] ?? self::CACHE_USAGE_READ_ONLY;
 }
 if (!isset($cache['region'])) {
 $cache['region'] = strtolower(str_replace('\\', '_', $this->rootEntityName)) . '__' . $fieldName;
 }
 return $cache;
 }
 public function setChangeTrackingPolicy($policy)
 {
 $this->changeTrackingPolicy = $policy;
 }
 public function isChangeTrackingDeferredExplicit()
 {
 return $this->changeTrackingPolicy === self::CHANGETRACKING_DEFERRED_EXPLICIT;
 }
 public function isChangeTrackingDeferredImplicit()
 {
 return $this->changeTrackingPolicy === self::CHANGETRACKING_DEFERRED_IMPLICIT;
 }
 public function isChangeTrackingNotify()
 {
 return $this->changeTrackingPolicy === self::CHANGETRACKING_NOTIFY;
 }
 public function isIdentifier($fieldName)
 {
 if (!$this->identifier) {
 return \false;
 }
 if (!$this->isIdentifierComposite) {
 return $fieldName === $this->identifier[0];
 }
 return in_array($fieldName, $this->identifier, \true);
 }
 public function isUniqueField($fieldName)
 {
 $mapping = $this->getFieldMapping($fieldName);
 return $mapping !== \false && isset($mapping['unique']) && $mapping['unique'];
 }
 public function isNullable($fieldName)
 {
 $mapping = $this->getFieldMapping($fieldName);
 return $mapping !== \false && isset($mapping['nullable']) && $mapping['nullable'];
 }
 public function getColumnName($fieldName)
 {
 return $this->columnNames[$fieldName] ?? $fieldName;
 }
 public function getFieldMapping($fieldName)
 {
 if (!isset($this->fieldMappings[$fieldName])) {
 throw MappingException::mappingNotFound($this->name, $fieldName);
 }
 return $this->fieldMappings[$fieldName];
 }
 public function getAssociationMapping($fieldName)
 {
 if (!isset($this->associationMappings[$fieldName])) {
 throw MappingException::mappingNotFound($this->name, $fieldName);
 }
 return $this->associationMappings[$fieldName];
 }
 public function getAssociationMappings()
 {
 return $this->associationMappings;
 }
 public function getFieldName($columnName)
 {
 return $this->fieldNames[$columnName] ?? $columnName;
 }
 public function getNamedQuery($queryName)
 {
 if (!isset($this->namedQueries[$queryName])) {
 throw MappingException::queryNotFound($this->name, $queryName);
 }
 return $this->namedQueries[$queryName]['dql'];
 }
 public function getNamedQueries()
 {
 return $this->namedQueries;
 }
 public function getNamedNativeQuery($queryName)
 {
 if (!isset($this->namedNativeQueries[$queryName])) {
 throw MappingException::queryNotFound($this->name, $queryName);
 }
 return $this->namedNativeQueries[$queryName];
 }
 public function getNamedNativeQueries()
 {
 return $this->namedNativeQueries;
 }
 public function getSqlResultSetMapping($name)
 {
 if (!isset($this->sqlResultSetMappings[$name])) {
 throw MappingException::resultMappingNotFound($this->name, $name);
 }
 return $this->sqlResultSetMappings[$name];
 }
 public function getSqlResultSetMappings()
 {
 return $this->sqlResultSetMappings;
 }
 private function isTypedProperty(string $name) : bool
 {
 return PHP_VERSION_ID >= 70400 && isset($this->reflClass) && $this->reflClass->hasProperty($name) && $this->reflClass->getProperty($name)->hasType();
 }
 private function validateAndCompleteTypedFieldMapping(array $mapping) : array
 {
 $type = $this->reflClass->getProperty($mapping['fieldName'])->getType();
 if ($type) {
 if (!isset($mapping['type']) && $type instanceof ReflectionNamedType) {
 if (PHP_VERSION_ID >= 80100 && !$type->isBuiltin() && enum_exists($type->getName())) {
 $mapping['enumType'] = $type->getName();
 $reflection = new ReflectionEnum($type->getName());
 $type = $reflection->getBackingType();
 assert($type instanceof ReflectionNamedType);
 }
 switch ($type->getName()) {
 case DateInterval::class:
 $mapping['type'] = Types::DATEINTERVAL;
 break;
 case DateTime::class:
 $mapping['type'] = Types::DATETIME_MUTABLE;
 break;
 case DateTimeImmutable::class:
 $mapping['type'] = Types::DATETIME_IMMUTABLE;
 break;
 case 'array':
 $mapping['type'] = Types::JSON;
 break;
 case 'bool':
 $mapping['type'] = Types::BOOLEAN;
 break;
 case 'float':
 $mapping['type'] = Types::FLOAT;
 break;
 case 'int':
 $mapping['type'] = Types::INTEGER;
 break;
 case 'string':
 $mapping['type'] = Types::STRING;
 break;
 }
 }
 }
 return $mapping;
 }
 private function validateAndCompleteTypedAssociationMapping(array $mapping) : array
 {
 $type = $this->reflClass->getProperty($mapping['fieldName'])->getType();
 if ($type === null || ($mapping['type'] & self::TO_ONE) === 0) {
 return $mapping;
 }
 if (!isset($mapping['targetEntity']) && $type instanceof ReflectionNamedType) {
 $mapping['targetEntity'] = $type->getName();
 }
 return $mapping;
 }
 protected function validateAndCompleteFieldMapping(array $mapping) : array
 {
 // Check mandatory fields
 if (!isset($mapping['fieldName']) || !$mapping['fieldName']) {
 throw MappingException::missingFieldName($this->name);
 }
 if ($this->isTypedProperty($mapping['fieldName'])) {
 $mapping = $this->validateAndCompleteTypedFieldMapping($mapping);
 }
 if (!isset($mapping['type'])) {
 // Default to string
 $mapping['type'] = 'string';
 }
 // Complete fieldName and columnName mapping
 if (!isset($mapping['columnName'])) {
 $mapping['columnName'] = $this->namingStrategy->propertyToColumnName($mapping['fieldName'], $this->name);
 }
 if ($mapping['columnName'][0] === '`') {
 $mapping['columnName'] = trim($mapping['columnName'], '`');
 $mapping['quoted'] = \true;
 }
 $this->columnNames[$mapping['fieldName']] = $mapping['columnName'];
 if (isset($this->fieldNames[$mapping['columnName']]) || $this->discriminatorColumn && $this->discriminatorColumn['name'] === $mapping['columnName']) {
 throw MappingException::duplicateColumnName($this->name, $mapping['columnName']);
 }
 $this->fieldNames[$mapping['columnName']] = $mapping['fieldName'];
 // Complete id mapping
 if (isset($mapping['id']) && $mapping['id'] === \true) {
 if ($this->versionField === $mapping['fieldName']) {
 throw MappingException::cannotVersionIdField($this->name, $mapping['fieldName']);
 }
 if (!in_array($mapping['fieldName'], $this->identifier, \true)) {
 $this->identifier[] = $mapping['fieldName'];
 }
 // Check for composite key
 if (!$this->isIdentifierComposite && count($this->identifier) > 1) {
 $this->isIdentifierComposite = \true;
 }
 }
 if (Type::hasType($mapping['type']) && Type::getType($mapping['type'])->canRequireSQLConversion()) {
 if (isset($mapping['id']) && $mapping['id'] === \true) {
 throw MappingException::sqlConversionNotAllowedForIdentifiers($this->name, $mapping['fieldName'], $mapping['type']);
 }
 $mapping['requireSQLConversion'] = \true;
 }
 if (isset($mapping['generated'])) {
 if (!in_array($mapping['generated'], [self::GENERATED_NEVER, self::GENERATED_INSERT, self::GENERATED_ALWAYS])) {
 throw MappingException::invalidGeneratedMode($mapping['generated']);
 }
 if ($mapping['generated'] === self::GENERATED_NEVER) {
 unset($mapping['generated']);
 }
 }
 if (isset($mapping['enumType'])) {
 if (PHP_VERSION_ID < 80100) {
 throw MappingException::enumsRequirePhp81($this->name, $mapping['fieldName']);
 }
 if (!enum_exists($mapping['enumType'])) {
 throw MappingException::nonEnumTypeMapped($this->name, $mapping['fieldName'], $mapping['enumType']);
 }
 }
 return $mapping;
 }
 protected function _validateAndCompleteAssociationMapping(array $mapping)
 {
 if (!isset($mapping['mappedBy'])) {
 $mapping['mappedBy'] = null;
 }
 if (!isset($mapping['inversedBy'])) {
 $mapping['inversedBy'] = null;
 }
 $mapping['isOwningSide'] = \true;
 // assume owning side until we hit mappedBy
 if (empty($mapping['indexBy'])) {
 unset($mapping['indexBy']);
 }
 // If targetEntity is unqualified, assume it is in the same namespace as
 // the sourceEntity.
 $mapping['sourceEntity'] = $this->name;
 if ($this->isTypedProperty($mapping['fieldName'])) {
 $mapping = $this->validateAndCompleteTypedAssociationMapping($mapping);
 }
 if (isset($mapping['targetEntity'])) {
 $mapping['targetEntity'] = $this->fullyQualifiedClassName($mapping['targetEntity']);
 $mapping['targetEntity'] = ltrim($mapping['targetEntity'], '\\');
 }
 if (($mapping['type'] & self::MANY_TO_ONE) > 0 && isset($mapping['orphanRemoval']) && $mapping['orphanRemoval']) {
 throw MappingException::illegalOrphanRemoval($this->name, $mapping['fieldName']);
 }
 // Complete id mapping
 if (isset($mapping['id']) && $mapping['id'] === \true) {
 if (isset($mapping['orphanRemoval']) && $mapping['orphanRemoval']) {
 throw MappingException::illegalOrphanRemovalOnIdentifierAssociation($this->name, $mapping['fieldName']);
 }
 if (!in_array($mapping['fieldName'], $this->identifier, \true)) {
 if (isset($mapping['joinColumns']) && count($mapping['joinColumns']) >= 2) {
 throw MappingException::cannotMapCompositePrimaryKeyEntitiesAsForeignId($mapping['targetEntity'], $this->name, $mapping['fieldName']);
 }
 $this->identifier[] = $mapping['fieldName'];
 $this->containsForeignIdentifier = \true;
 }
 // Check for composite key
 if (!$this->isIdentifierComposite && count($this->identifier) > 1) {
 $this->isIdentifierComposite = \true;
 }
 if ($this->cache && !isset($mapping['cache'])) {
 throw NonCacheableEntityAssociation::fromEntityAndField($this->name, $mapping['fieldName']);
 }
 }
 // Mandatory attributes for both sides
 // Mandatory: fieldName, targetEntity
 if (!isset($mapping['fieldName']) || !$mapping['fieldName']) {
 throw MappingException::missingFieldName($this->name);
 }
 if (!isset($mapping['targetEntity'])) {
 throw MappingException::missingTargetEntity($mapping['fieldName']);
 }
 // Mandatory and optional attributes for either side
 if (!$mapping['mappedBy']) {
 if (isset($mapping['joinTable']) && $mapping['joinTable']) {
 if (isset($mapping['joinTable']['name']) && $mapping['joinTable']['name'][0] === '`') {
 $mapping['joinTable']['name'] = trim($mapping['joinTable']['name'], '`');
 $mapping['joinTable']['quoted'] = \true;
 }
 }
 } else {
 $mapping['isOwningSide'] = \false;
 }
 if (isset($mapping['id']) && $mapping['id'] === \true && $mapping['type'] & self::TO_MANY) {
 throw MappingException::illegalToManyIdentifierAssociation($this->name, $mapping['fieldName']);
 }
 // Fetch mode. Default fetch mode to LAZY, if not set.
 if (!isset($mapping['fetch'])) {
 $mapping['fetch'] = self::FETCH_LAZY;
 }
 // Cascades
 $cascades = isset($mapping['cascade']) ? array_map('strtolower', $mapping['cascade']) : [];
 $allCascades = ['remove', 'persist', 'refresh', 'merge', 'detach'];
 if (in_array('all', $cascades, \true)) {
 $cascades = $allCascades;
 } elseif (count($cascades) !== count(array_intersect($cascades, $allCascades))) {
 throw MappingException::invalidCascadeOption(array_diff($cascades, $allCascades), $this->name, $mapping['fieldName']);
 }
 $mapping['cascade'] = $cascades;
 $mapping['isCascadeRemove'] = in_array('remove', $cascades, \true);
 $mapping['isCascadePersist'] = in_array('persist', $cascades, \true);
 $mapping['isCascadeRefresh'] = in_array('refresh', $cascades, \true);
 $mapping['isCascadeMerge'] = in_array('merge', $cascades, \true);
 $mapping['isCascadeDetach'] = in_array('detach', $cascades, \true);
 return $mapping;
 }
 protected function _validateAndCompleteOneToOneMapping(array $mapping)
 {
 $mapping = $this->_validateAndCompleteAssociationMapping($mapping);
 if (isset($mapping['joinColumns']) && $mapping['joinColumns']) {
 $mapping['isOwningSide'] = \true;
 }
 if ($mapping['isOwningSide']) {
 if (empty($mapping['joinColumns'])) {
 // Apply default join column
 $mapping['joinColumns'] = [['name' => $this->namingStrategy->joinColumnName($mapping['fieldName'], $this->name), 'referencedColumnName' => $this->namingStrategy->referenceColumnName()]];
 }
 $uniqueConstraintColumns = [];
 foreach ($mapping['joinColumns'] as &$joinColumn) {
 if ($mapping['type'] === self::ONE_TO_ONE && !$this->isInheritanceTypeSingleTable()) {
 if (count($mapping['joinColumns']) === 1) {
 if (empty($mapping['id'])) {
 $joinColumn['unique'] = \true;
 }
 } else {
 $uniqueConstraintColumns[] = $joinColumn['name'];
 }
 }
 if (empty($joinColumn['name'])) {
 $joinColumn['name'] = $this->namingStrategy->joinColumnName($mapping['fieldName'], $this->name);
 }
 if (empty($joinColumn['referencedColumnName'])) {
 $joinColumn['referencedColumnName'] = $this->namingStrategy->referenceColumnName();
 }
 if ($joinColumn['name'][0] === '`') {
 $joinColumn['name'] = trim($joinColumn['name'], '`');
 $joinColumn['quoted'] = \true;
 }
 if ($joinColumn['referencedColumnName'][0] === '`') {
 $joinColumn['referencedColumnName'] = trim($joinColumn['referencedColumnName'], '`');
 $joinColumn['quoted'] = \true;
 }
 $mapping['sourceToTargetKeyColumns'][$joinColumn['name']] = $joinColumn['referencedColumnName'];
 $mapping['joinColumnFieldNames'][$joinColumn['name']] = $joinColumn['fieldName'] ?? $joinColumn['name'];
 }
 if ($uniqueConstraintColumns) {
 if (!$this->table) {
 throw new RuntimeException('ClassMetadataInfo::setTable() has to be called before defining a one to one relationship.');
 }
 $this->table['uniqueConstraints'][$mapping['fieldName'] . '_uniq'] = ['columns' => $uniqueConstraintColumns];
 }
 $mapping['targetToSourceKeyColumns'] = array_flip($mapping['sourceToTargetKeyColumns']);
 }
 $mapping['orphanRemoval'] = isset($mapping['orphanRemoval']) && $mapping['orphanRemoval'];
 $mapping['isCascadeRemove'] = $mapping['orphanRemoval'] || $mapping['isCascadeRemove'];
 if ($mapping['orphanRemoval']) {
 unset($mapping['unique']);
 }
 if (isset($mapping['id']) && $mapping['id'] === \true && !$mapping['isOwningSide']) {
 throw MappingException::illegalInverseIdentifierAssociation($this->name, $mapping['fieldName']);
 }
 return $mapping;
 }
 protected function _validateAndCompleteOneToManyMapping(array $mapping)
 {
 $mapping = $this->_validateAndCompleteAssociationMapping($mapping);
 // OneToMany-side MUST be inverse (must have mappedBy)
 if (!isset($mapping['mappedBy'])) {
 throw MappingException::oneToManyRequiresMappedBy($this->name, $mapping['fieldName']);
 }
 $mapping['orphanRemoval'] = isset($mapping['orphanRemoval']) && $mapping['orphanRemoval'];
 $mapping['isCascadeRemove'] = $mapping['orphanRemoval'] || $mapping['isCascadeRemove'];
 $this->assertMappingOrderBy($mapping);
 return $mapping;
 }
 protected function _validateAndCompleteManyToManyMapping(array $mapping)
 {
 $mapping = $this->_validateAndCompleteAssociationMapping($mapping);
 if ($mapping['isOwningSide']) {
 // owning side MUST have a join table
 if (!isset($mapping['joinTable']['name'])) {
 $mapping['joinTable']['name'] = $this->namingStrategy->joinTableName($mapping['sourceEntity'], $mapping['targetEntity'], $mapping['fieldName']);
 }
 $selfReferencingEntityWithoutJoinColumns = $mapping['sourceEntity'] === $mapping['targetEntity'] && !(isset($mapping['joinTable']['joinColumns']) || isset($mapping['joinTable']['inverseJoinColumns']));
 if (!isset($mapping['joinTable']['joinColumns'])) {
 $mapping['joinTable']['joinColumns'] = [['name' => $this->namingStrategy->joinKeyColumnName($mapping['sourceEntity'], $selfReferencingEntityWithoutJoinColumns ? 'source' : null), 'referencedColumnName' => $this->namingStrategy->referenceColumnName(), 'onDelete' => 'CASCADE']];
 }
 if (!isset($mapping['joinTable']['inverseJoinColumns'])) {
 $mapping['joinTable']['inverseJoinColumns'] = [['name' => $this->namingStrategy->joinKeyColumnName($mapping['targetEntity'], $selfReferencingEntityWithoutJoinColumns ? 'target' : null), 'referencedColumnName' => $this->namingStrategy->referenceColumnName(), 'onDelete' => 'CASCADE']];
 }
 $mapping['joinTableColumns'] = [];
 foreach ($mapping['joinTable']['joinColumns'] as &$joinColumn) {
 if (empty($joinColumn['name'])) {
 $joinColumn['name'] = $this->namingStrategy->joinKeyColumnName($mapping['sourceEntity'], $joinColumn['referencedColumnName']);
 }
 if (empty($joinColumn['referencedColumnName'])) {
 $joinColumn['referencedColumnName'] = $this->namingStrategy->referenceColumnName();
 }
 if ($joinColumn['name'][0] === '`') {
 $joinColumn['name'] = trim($joinColumn['name'], '`');
 $joinColumn['quoted'] = \true;
 }
 if ($joinColumn['referencedColumnName'][0] === '`') {
 $joinColumn['referencedColumnName'] = trim($joinColumn['referencedColumnName'], '`');
 $joinColumn['quoted'] = \true;
 }
 if (isset($joinColumn['onDelete']) && strtolower($joinColumn['onDelete']) === 'cascade') {
 $mapping['isOnDeleteCascade'] = \true;
 }
 $mapping['relationToSourceKeyColumns'][$joinColumn['name']] = $joinColumn['referencedColumnName'];
 $mapping['joinTableColumns'][] = $joinColumn['name'];
 }
 foreach ($mapping['joinTable']['inverseJoinColumns'] as &$inverseJoinColumn) {
 if (empty($inverseJoinColumn['name'])) {
 $inverseJoinColumn['name'] = $this->namingStrategy->joinKeyColumnName($mapping['targetEntity'], $inverseJoinColumn['referencedColumnName']);
 }
 if (empty($inverseJoinColumn['referencedColumnName'])) {
 $inverseJoinColumn['referencedColumnName'] = $this->namingStrategy->referenceColumnName();
 }
 if ($inverseJoinColumn['name'][0] === '`') {
 $inverseJoinColumn['name'] = trim($inverseJoinColumn['name'], '`');
 $inverseJoinColumn['quoted'] = \true;
 }
 if ($inverseJoinColumn['referencedColumnName'][0] === '`') {
 $inverseJoinColumn['referencedColumnName'] = trim($inverseJoinColumn['referencedColumnName'], '`');
 $inverseJoinColumn['quoted'] = \true;
 }
 if (isset($inverseJoinColumn['onDelete']) && strtolower($inverseJoinColumn['onDelete']) === 'cascade') {
 $mapping['isOnDeleteCascade'] = \true;
 }
 $mapping['relationToTargetKeyColumns'][$inverseJoinColumn['name']] = $inverseJoinColumn['referencedColumnName'];
 $mapping['joinTableColumns'][] = $inverseJoinColumn['name'];
 }
 }
 $mapping['orphanRemoval'] = isset($mapping['orphanRemoval']) && $mapping['orphanRemoval'];
 $this->assertMappingOrderBy($mapping);
 return $mapping;
 }
 public function getIdentifierFieldNames()
 {
 return $this->identifier;
 }
 public function getSingleIdentifierFieldName()
 {
 if ($this->isIdentifierComposite) {
 throw MappingException::singleIdNotAllowedOnCompositePrimaryKey($this->name);
 }
 if (!isset($this->identifier[0])) {
 throw MappingException::noIdDefined($this->name);
 }
 return $this->identifier[0];
 }
 public function getSingleIdentifierColumnName()
 {
 return $this->getColumnName($this->getSingleIdentifierFieldName());
 }
 public function setIdentifier(array $identifier)
 {
 $this->identifier = $identifier;
 $this->isIdentifierComposite = count($this->identifier) > 1;
 }
 public function getIdentifier()
 {
 return $this->identifier;
 }
 public function hasField($fieldName)
 {
 return isset($this->fieldMappings[$fieldName]) || isset($this->embeddedClasses[$fieldName]);
 }
 public function getColumnNames(?array $fieldNames = null)
 {
 if ($fieldNames === null) {
 return array_keys($this->fieldNames);
 }
 return array_values(array_map([$this, 'getColumnName'], $fieldNames));
 }
 public function getIdentifierColumnNames()
 {
 $columnNames = [];
 foreach ($this->identifier as $idProperty) {
 if (isset($this->fieldMappings[$idProperty])) {
 $columnNames[] = $this->fieldMappings[$idProperty]['columnName'];
 continue;
 }
 // Association defined as Id field
 $joinColumns = $this->associationMappings[$idProperty]['joinColumns'];
 $assocColumnNames = array_map(static function ($joinColumn) {
 return $joinColumn['name'];
 }, $joinColumns);
 $columnNames = array_merge($columnNames, $assocColumnNames);
 }
 return $columnNames;
 }
 public function setIdGeneratorType($generatorType)
 {
 $this->generatorType = $generatorType;
 }
 public function usesIdGenerator()
 {
 return $this->generatorType !== self::GENERATOR_TYPE_NONE;
 }
 public function isInheritanceTypeNone()
 {
 return $this->inheritanceType === self::INHERITANCE_TYPE_NONE;
 }
 public function isInheritanceTypeJoined()
 {
 return $this->inheritanceType === self::INHERITANCE_TYPE_JOINED;
 }
 public function isInheritanceTypeSingleTable()
 {
 return $this->inheritanceType === self::INHERITANCE_TYPE_SINGLE_TABLE;
 }
 public function isInheritanceTypeTablePerClass()
 {
 return $this->inheritanceType === self::INHERITANCE_TYPE_TABLE_PER_CLASS;
 }
 public function isIdGeneratorIdentity()
 {
 return $this->generatorType === self::GENERATOR_TYPE_IDENTITY;
 }
 public function isIdGeneratorSequence()
 {
 return $this->generatorType === self::GENERATOR_TYPE_SEQUENCE;
 }
 public function isIdGeneratorTable()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9046', '%s is deprecated', __METHOD__);
 return \false;
 }
 public function isIdentifierNatural()
 {
 return $this->generatorType === self::GENERATOR_TYPE_NONE;
 }
 public function isIdentifierUuid()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9046', '%s is deprecated', __METHOD__);
 return $this->generatorType === self::GENERATOR_TYPE_UUID;
 }
 public function getTypeOfField($fieldName)
 {
 return isset($this->fieldMappings[$fieldName]) ? $this->fieldMappings[$fieldName]['type'] : null;
 }
 public function getTypeOfColumn($columnName)
 {
 return $this->getTypeOfField($this->getFieldName($columnName));
 }
 public function getTableName()
 {
 return $this->table['name'];
 }
 public function getSchemaName()
 {
 return $this->table['schema'] ?? null;
 }
 public function getTemporaryIdTableName()
 {
 // replace dots with underscores because PostgreSQL creates temporary tables in a special schema
 return str_replace('.', '_', $this->getTableName() . '_id_tmp');
 }
 public function setSubclasses(array $subclasses)
 {
 foreach ($subclasses as $subclass) {
 $this->subClasses[] = $this->fullyQualifiedClassName($subclass);
 }
 }
 public function setParentClasses(array $classNames)
 {
 $this->parentClasses = $classNames;
 if (count($classNames) > 0) {
 $this->rootEntityName = array_pop($classNames);
 }
 }
 public function setInheritanceType($type)
 {
 if (!$this->isInheritanceType($type)) {
 throw MappingException::invalidInheritanceType($this->name, $type);
 }
 $this->inheritanceType = $type;
 }
 public function setAssociationOverride($fieldName, array $overrideMapping)
 {
 if (!isset($this->associationMappings[$fieldName])) {
 throw MappingException::invalidOverrideFieldName($this->name, $fieldName);
 }
 $mapping = $this->associationMappings[$fieldName];
 //if (isset($mapping['inherited']) && (count($overrideMapping) !== 1 || ! isset($overrideMapping['fetch']))) {
 // TODO: Deprecate overriding the fetch mode via association override for 3.0,
 // users should do this with a listener and a custom attribute/annotation
 // TODO: Enable this exception in 2.8
 //throw MappingException::illegalOverrideOfInheritedProperty($this->name, $fieldName);
 //}
 if (isset($overrideMapping['joinColumns'])) {
 $mapping['joinColumns'] = $overrideMapping['joinColumns'];
 }
 if (isset($overrideMapping['inversedBy'])) {
 $mapping['inversedBy'] = $overrideMapping['inversedBy'];
 }
 if (isset($overrideMapping['joinTable'])) {
 $mapping['joinTable'] = $overrideMapping['joinTable'];
 }
 if (isset($overrideMapping['fetch'])) {
 $mapping['fetch'] = $overrideMapping['fetch'];
 }
 $mapping['joinColumnFieldNames'] = null;
 $mapping['joinTableColumns'] = null;
 $mapping['sourceToTargetKeyColumns'] = null;
 $mapping['relationToSourceKeyColumns'] = null;
 $mapping['relationToTargetKeyColumns'] = null;
 switch ($mapping['type']) {
 case self::ONE_TO_ONE:
 $mapping = $this->_validateAndCompleteOneToOneMapping($mapping);
 break;
 case self::ONE_TO_MANY:
 $mapping = $this->_validateAndCompleteOneToManyMapping($mapping);
 break;
 case self::MANY_TO_ONE:
 $mapping = $this->_validateAndCompleteOneToOneMapping($mapping);
 break;
 case self::MANY_TO_MANY:
 $mapping = $this->_validateAndCompleteManyToManyMapping($mapping);
 break;
 }
 $this->associationMappings[$fieldName] = $mapping;
 }
 public function setAttributeOverride($fieldName, array $overrideMapping)
 {
 if (!isset($this->fieldMappings[$fieldName])) {
 throw MappingException::invalidOverrideFieldName($this->name, $fieldName);
 }
 $mapping = $this->fieldMappings[$fieldName];
 //if (isset($mapping['inherited'])) {
 // TODO: Enable this exception in 2.8
 //throw MappingException::illegalOverrideOfInheritedProperty($this->name, $fieldName);
 //}
 if (isset($mapping['id'])) {
 $overrideMapping['id'] = $mapping['id'];
 }
 if (!isset($overrideMapping['type'])) {
 $overrideMapping['type'] = $mapping['type'];
 }
 if (!isset($overrideMapping['fieldName'])) {
 $overrideMapping['fieldName'] = $mapping['fieldName'];
 }
 if ($overrideMapping['type'] !== $mapping['type']) {
 throw MappingException::invalidOverrideFieldType($this->name, $fieldName);
 }
 unset($this->fieldMappings[$fieldName]);
 unset($this->fieldNames[$mapping['columnName']]);
 unset($this->columnNames[$mapping['fieldName']]);
 $overrideMapping = $this->validateAndCompleteFieldMapping($overrideMapping);
 $this->fieldMappings[$fieldName] = $overrideMapping;
 }
 public function isInheritedField($fieldName)
 {
 return isset($this->fieldMappings[$fieldName]['inherited']);
 }
 public function isRootEntity()
 {
 return $this->name === $this->rootEntityName;
 }
 public function isInheritedAssociation($fieldName)
 {
 return isset($this->associationMappings[$fieldName]['inherited']);
 }
 public function isInheritedEmbeddedClass($fieldName)
 {
 return isset($this->embeddedClasses[$fieldName]['inherited']);
 }
 public function setTableName($tableName)
 {
 $this->table['name'] = $tableName;
 }
 public function setPrimaryTable(array $table)
 {
 if (isset($table['name'])) {
 // Split schema and table name from a table name like "myschema.mytable"
 if (strpos($table['name'], '.') !== \false) {
 [$this->table['schema'], $table['name']] = explode('.', $table['name'], 2);
 }
 if ($table['name'][0] === '`') {
 $table['name'] = trim($table['name'], '`');
 $this->table['quoted'] = \true;
 }
 $this->table['name'] = $table['name'];
 }
 if (isset($table['quoted'])) {
 $this->table['quoted'] = $table['quoted'];
 }
 if (isset($table['schema'])) {
 $this->table['schema'] = $table['schema'];
 }
 if (isset($table['indexes'])) {
 $this->table['indexes'] = $table['indexes'];
 }
 if (isset($table['uniqueConstraints'])) {
 $this->table['uniqueConstraints'] = $table['uniqueConstraints'];
 }
 if (isset($table['options'])) {
 $this->table['options'] = $table['options'];
 }
 }
 private function isInheritanceType(int $type) : bool
 {
 return $type === self::INHERITANCE_TYPE_NONE || $type === self::INHERITANCE_TYPE_SINGLE_TABLE || $type === self::INHERITANCE_TYPE_JOINED || $type === self::INHERITANCE_TYPE_TABLE_PER_CLASS;
 }
 public function mapField(array $mapping)
 {
 $mapping = $this->validateAndCompleteFieldMapping($mapping);
 $this->assertFieldNotMapped($mapping['fieldName']);
 if (isset($mapping['generated'])) {
 $this->requiresFetchAfterChange = \true;
 }
 $this->fieldMappings[$mapping['fieldName']] = $mapping;
 }
 public function addInheritedAssociationMapping(array $mapping)
 {
 if (isset($this->associationMappings[$mapping['fieldName']])) {
 throw MappingException::duplicateAssociationMapping($this->name, $mapping['fieldName']);
 }
 $this->associationMappings[$mapping['fieldName']] = $mapping;
 }
 public function addInheritedFieldMapping(array $fieldMapping)
 {
 $this->fieldMappings[$fieldMapping['fieldName']] = $fieldMapping;
 $this->columnNames[$fieldMapping['fieldName']] = $fieldMapping['columnName'];
 $this->fieldNames[$fieldMapping['columnName']] = $fieldMapping['fieldName'];
 }
 public function addNamedQuery(array $queryMapping)
 {
 if (!isset($queryMapping['name'])) {
 throw MappingException::nameIsMandatoryForQueryMapping($this->name);
 }
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8592', 'Named Queries are deprecated, here "%s" on entity %s. Move the query logic into EntityRepository', $queryMapping['name'], $this->name);
 if (isset($this->namedQueries[$queryMapping['name']])) {
 throw MappingException::duplicateQueryMapping($this->name, $queryMapping['name']);
 }
 if (!isset($queryMapping['query'])) {
 throw MappingException::emptyQueryMapping($this->name, $queryMapping['name']);
 }
 $name = $queryMapping['name'];
 $query = $queryMapping['query'];
 $dql = str_replace('__CLASS__', $this->name, $query);
 $this->namedQueries[$name] = ['name' => $name, 'query' => $query, 'dql' => $dql];
 }
 public function addNamedNativeQuery(array $queryMapping)
 {
 if (!isset($queryMapping['name'])) {
 throw MappingException::nameIsMandatoryForQueryMapping($this->name);
 }
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8592', 'Named Native Queries are deprecated, here "%s" on entity %s. Move the query logic into EntityRepository', $queryMapping['name'], $this->name);
 if (isset($this->namedNativeQueries[$queryMapping['name']])) {
 throw MappingException::duplicateQueryMapping($this->name, $queryMapping['name']);
 }
 if (!isset($queryMapping['query'])) {
 throw MappingException::emptyQueryMapping($this->name, $queryMapping['name']);
 }
 if (!isset($queryMapping['resultClass']) && !isset($queryMapping['resultSetMapping'])) {
 throw MappingException::missingQueryMapping($this->name, $queryMapping['name']);
 }
 $queryMapping['isSelfClass'] = \false;
 if (isset($queryMapping['resultClass'])) {
 if ($queryMapping['resultClass'] === '__CLASS__') {
 $queryMapping['isSelfClass'] = \true;
 $queryMapping['resultClass'] = $this->name;
 }
 $queryMapping['resultClass'] = $this->fullyQualifiedClassName($queryMapping['resultClass']);
 $queryMapping['resultClass'] = ltrim($queryMapping['resultClass'], '\\');
 }
 $this->namedNativeQueries[$queryMapping['name']] = $queryMapping;
 }
 public function addSqlResultSetMapping(array $resultMapping)
 {
 if (!isset($resultMapping['name'])) {
 throw MappingException::nameIsMandatoryForSqlResultSetMapping($this->name);
 }
 if (isset($this->sqlResultSetMappings[$resultMapping['name']])) {
 throw MappingException::duplicateResultSetMapping($this->name, $resultMapping['name']);
 }
 if (isset($resultMapping['entities'])) {
 foreach ($resultMapping['entities'] as $key => $entityResult) {
 if (!isset($entityResult['entityClass'])) {
 throw MappingException::missingResultSetMappingEntity($this->name, $resultMapping['name']);
 }
 $entityResult['isSelfClass'] = \false;
 if ($entityResult['entityClass'] === '__CLASS__') {
 $entityResult['isSelfClass'] = \true;
 $entityResult['entityClass'] = $this->name;
 }
 $entityResult['entityClass'] = $this->fullyQualifiedClassName($entityResult['entityClass']);
 $resultMapping['entities'][$key]['entityClass'] = ltrim($entityResult['entityClass'], '\\');
 $resultMapping['entities'][$key]['isSelfClass'] = $entityResult['isSelfClass'];
 if (isset($entityResult['fields'])) {
 foreach ($entityResult['fields'] as $k => $field) {
 if (!isset($field['name'])) {
 throw MappingException::missingResultSetMappingFieldName($this->name, $resultMapping['name']);
 }
 if (!isset($field['column'])) {
 $fieldName = $field['name'];
 if (strpos($fieldName, '.')) {
 [, $fieldName] = explode('.', $fieldName);
 }
 $resultMapping['entities'][$key]['fields'][$k]['column'] = $fieldName;
 }
 }
 }
 }
 }
 $this->sqlResultSetMappings[$resultMapping['name']] = $resultMapping;
 }
 public function mapOneToOne(array $mapping)
 {
 $mapping['type'] = self::ONE_TO_ONE;
 $mapping = $this->_validateAndCompleteOneToOneMapping($mapping);
 $this->_storeAssociationMapping($mapping);
 }
 public function mapOneToMany(array $mapping)
 {
 $mapping['type'] = self::ONE_TO_MANY;
 $mapping = $this->_validateAndCompleteOneToManyMapping($mapping);
 $this->_storeAssociationMapping($mapping);
 }
 public function mapManyToOne(array $mapping)
 {
 $mapping['type'] = self::MANY_TO_ONE;
 // A many-to-one mapping is essentially a one-one backreference
 $mapping = $this->_validateAndCompleteOneToOneMapping($mapping);
 $this->_storeAssociationMapping($mapping);
 }
 public function mapManyToMany(array $mapping)
 {
 $mapping['type'] = self::MANY_TO_MANY;
 $mapping = $this->_validateAndCompleteManyToManyMapping($mapping);
 $this->_storeAssociationMapping($mapping);
 }
 protected function _storeAssociationMapping(array $assocMapping)
 {
 $sourceFieldName = $assocMapping['fieldName'];
 $this->assertFieldNotMapped($sourceFieldName);
 $this->associationMappings[$sourceFieldName] = $assocMapping;
 }
 public function setCustomRepositoryClass($repositoryClassName)
 {
 $this->customRepositoryClassName = $this->fullyQualifiedClassName($repositoryClassName);
 }
 public function invokeLifecycleCallbacks($lifecycleEvent, $entity)
 {
 foreach ($this->lifecycleCallbacks[$lifecycleEvent] as $callback) {
 $entity->{$callback}();
 }
 }
 public function hasLifecycleCallbacks($lifecycleEvent)
 {
 return isset($this->lifecycleCallbacks[$lifecycleEvent]);
 }
 public function getLifecycleCallbacks($event)
 {
 return $this->lifecycleCallbacks[$event] ?? [];
 }
 public function addLifecycleCallback($callback, $event)
 {
 if ($this->isEmbeddedClass) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/8381', 'Registering lifecycle callback %s on Embedded class %s is not doing anything and will throw exception in 3.0', $event, $this->name);
 }
 if (isset($this->lifecycleCallbacks[$event]) && in_array($callback, $this->lifecycleCallbacks[$event], \true)) {
 return;
 }
 $this->lifecycleCallbacks[$event][] = $callback;
 }
 public function setLifecycleCallbacks(array $callbacks)
 {
 $this->lifecycleCallbacks = $callbacks;
 }
 public function addEntityListener($eventName, $class, $method)
 {
 $class = $this->fullyQualifiedClassName($class);
 $listener = ['class' => $class, 'method' => $method];
 if (!class_exists($class)) {
 throw MappingException::entityListenerClassNotFound($class, $this->name);
 }
 if (!method_exists($class, $method)) {
 throw MappingException::entityListenerMethodNotFound($class, $method, $this->name);
 }
 if (isset($this->entityListeners[$eventName]) && in_array($listener, $this->entityListeners[$eventName], \true)) {
 throw MappingException::duplicateEntityListener($class, $method, $this->name);
 }
 $this->entityListeners[$eventName][] = $listener;
 }
 public function setDiscriminatorColumn($columnDef)
 {
 if ($columnDef !== null) {
 if (!isset($columnDef['name'])) {
 throw MappingException::nameIsMandatoryForDiscriminatorColumns($this->name);
 }
 if (isset($this->fieldNames[$columnDef['name']])) {
 throw MappingException::duplicateColumnName($this->name, $columnDef['name']);
 }
 if (!isset($columnDef['fieldName'])) {
 $columnDef['fieldName'] = $columnDef['name'];
 }
 if (!isset($columnDef['type'])) {
 $columnDef['type'] = 'string';
 }
 if (in_array($columnDef['type'], ['boolean', 'array', 'object', 'datetime', 'time', 'date'], \true)) {
 throw MappingException::invalidDiscriminatorColumnType($this->name, $columnDef['type']);
 }
 $this->discriminatorColumn = $columnDef;
 }
 }
 public final function getDiscriminatorColumn() : array
 {
 if ($this->discriminatorColumn === null) {
 throw new LogicException('The discriminator column was not set.');
 }
 return $this->discriminatorColumn;
 }
 public function setDiscriminatorMap(array $map)
 {
 foreach ($map as $value => $className) {
 $this->addDiscriminatorMapClass($value, $className);
 }
 }
 public function addDiscriminatorMapClass($name, $className)
 {
 $className = $this->fullyQualifiedClassName($className);
 $className = ltrim($className, '\\');
 $this->discriminatorMap[$name] = $className;
 if ($this->name === $className) {
 $this->discriminatorValue = $name;
 return;
 }
 if (!(class_exists($className) || interface_exists($className))) {
 throw MappingException::invalidClassInDiscriminatorMap($className, $this->name);
 }
 if (is_subclass_of($className, $this->name) && !in_array($className, $this->subClasses, \true)) {
 $this->subClasses[] = $className;
 }
 }
 public function hasNamedQuery($queryName)
 {
 return isset($this->namedQueries[$queryName]);
 }
 public function hasNamedNativeQuery($queryName)
 {
 return isset($this->namedNativeQueries[$queryName]);
 }
 public function hasSqlResultSetMapping($name)
 {
 return isset($this->sqlResultSetMappings[$name]);
 }
 public function hasAssociation($fieldName)
 {
 return isset($this->associationMappings[$fieldName]);
 }
 public function isSingleValuedAssociation($fieldName)
 {
 return isset($this->associationMappings[$fieldName]) && $this->associationMappings[$fieldName]['type'] & self::TO_ONE;
 }
 public function isCollectionValuedAssociation($fieldName)
 {
 return isset($this->associationMappings[$fieldName]) && !($this->associationMappings[$fieldName]['type'] & self::TO_ONE);
 }
 public function isAssociationWithSingleJoinColumn($fieldName)
 {
 return isset($this->associationMappings[$fieldName]) && isset($this->associationMappings[$fieldName]['joinColumns'][0]) && !isset($this->associationMappings[$fieldName]['joinColumns'][1]);
 }
 public function getSingleAssociationJoinColumnName($fieldName)
 {
 if (!$this->isAssociationWithSingleJoinColumn($fieldName)) {
 throw MappingException::noSingleAssociationJoinColumnFound($this->name, $fieldName);
 }
 return $this->associationMappings[$fieldName]['joinColumns'][0]['name'];
 }
 public function getSingleAssociationReferencedJoinColumnName($fieldName)
 {
 if (!$this->isAssociationWithSingleJoinColumn($fieldName)) {
 throw MappingException::noSingleAssociationJoinColumnFound($this->name, $fieldName);
 }
 return $this->associationMappings[$fieldName]['joinColumns'][0]['referencedColumnName'];
 }
 public function getFieldForColumn($columnName)
 {
 if (isset($this->fieldNames[$columnName])) {
 return $this->fieldNames[$columnName];
 }
 foreach ($this->associationMappings as $assocName => $mapping) {
 if ($this->isAssociationWithSingleJoinColumn($assocName) && $this->associationMappings[$assocName]['joinColumns'][0]['name'] === $columnName) {
 return $assocName;
 }
 }
 throw MappingException::noFieldNameFoundForColumn($this->name, $columnName);
 }
 public function setIdGenerator($generator)
 {
 $this->idGenerator = $generator;
 }
 public function setCustomGeneratorDefinition(array $definition)
 {
 $this->customGeneratorDefinition = $definition;
 }
 public function setSequenceGeneratorDefinition(array $definition)
 {
 if (!isset($definition['sequenceName']) || trim($definition['sequenceName']) === '') {
 throw MappingException::missingSequenceName($this->name);
 }
 if ($definition['sequenceName'][0] === '`') {
 $definition['sequenceName'] = trim($definition['sequenceName'], '`');
 $definition['quoted'] = \true;
 }
 if (!isset($definition['allocationSize']) || trim((string) $definition['allocationSize']) === '') {
 $definition['allocationSize'] = '1';
 }
 if (!isset($definition['initialValue']) || trim((string) $definition['initialValue']) === '') {
 $definition['initialValue'] = '1';
 }
 $definition['allocationSize'] = (string) $definition['allocationSize'];
 $definition['initialValue'] = (string) $definition['initialValue'];
 $this->sequenceGeneratorDefinition = $definition;
 }
 public function setVersionMapping(array &$mapping)
 {
 $this->isVersioned = \true;
 $this->versionField = $mapping['fieldName'];
 $this->requiresFetchAfterChange = \true;
 if (!isset($mapping['default'])) {
 if (in_array($mapping['type'], ['integer', 'bigint', 'smallint'], \true)) {
 $mapping['default'] = 1;
 } elseif ($mapping['type'] === 'datetime') {
 $mapping['default'] = 'CURRENT_TIMESTAMP';
 } else {
 throw MappingException::unsupportedOptimisticLockingType($this->name, $mapping['fieldName'], $mapping['type']);
 }
 }
 }
 public function setVersioned($bool)
 {
 $this->isVersioned = $bool;
 if ($bool) {
 $this->requiresFetchAfterChange = \true;
 }
 }
 public function setVersionField($versionField)
 {
 $this->versionField = $versionField;
 }
 public function markReadOnly()
 {
 $this->isReadOnly = \true;
 }
 public function getFieldNames()
 {
 return array_keys($this->fieldMappings);
 }
 public function getAssociationNames()
 {
 return array_keys($this->associationMappings);
 }
 public function getAssociationTargetClass($assocName)
 {
 if (!isset($this->associationMappings[$assocName])) {
 throw new InvalidArgumentException("Association name expected, '" . $assocName . "' is not an association.");
 }
 return $this->associationMappings[$assocName]['targetEntity'];
 }
 public function getName()
 {
 return $this->name;
 }
 public function getQuotedIdentifierColumnNames($platform)
 {
 $quotedColumnNames = [];
 foreach ($this->identifier as $idProperty) {
 if (isset($this->fieldMappings[$idProperty])) {
 $quotedColumnNames[] = isset($this->fieldMappings[$idProperty]['quoted']) ? $platform->quoteIdentifier($this->fieldMappings[$idProperty]['columnName']) : $this->fieldMappings[$idProperty]['columnName'];
 continue;
 }
 // Association defined as Id field
 $joinColumns = $this->associationMappings[$idProperty]['joinColumns'];
 $assocQuotedColumnNames = array_map(static function ($joinColumn) use($platform) {
 return isset($joinColumn['quoted']) ? $platform->quoteIdentifier($joinColumn['name']) : $joinColumn['name'];
 }, $joinColumns);
 $quotedColumnNames = array_merge($quotedColumnNames, $assocQuotedColumnNames);
 }
 return $quotedColumnNames;
 }
 public function getQuotedColumnName($field, $platform)
 {
 return isset($this->fieldMappings[$field]['quoted']) ? $platform->quoteIdentifier($this->fieldMappings[$field]['columnName']) : $this->fieldMappings[$field]['columnName'];
 }
 public function getQuotedTableName($platform)
 {
 return isset($this->table['quoted']) ? $platform->quoteIdentifier($this->table['name']) : $this->table['name'];
 }
 public function getQuotedJoinTableName(array $assoc, $platform)
 {
 return isset($assoc['joinTable']['quoted']) ? $platform->quoteIdentifier($assoc['joinTable']['name']) : $assoc['joinTable']['name'];
 }
 public function isAssociationInverseSide($fieldName)
 {
 return isset($this->associationMappings[$fieldName]) && !$this->associationMappings[$fieldName]['isOwningSide'];
 }
 public function getAssociationMappedByTargetField($fieldName)
 {
 return $this->associationMappings[$fieldName]['mappedBy'];
 }
 public function getAssociationsByTargetClass($targetClass)
 {
 $relations = [];
 foreach ($this->associationMappings as $mapping) {
 if ($mapping['targetEntity'] === $targetClass) {
 $relations[$mapping['fieldName']] = $mapping;
 }
 }
 return $relations;
 }
 public function fullyQualifiedClassName($className)
 {
 if (empty($className)) {
 return $className;
 }
 if (strpos($className, '\\') === \false && $this->namespace) {
 return $this->namespace . '\\' . $className;
 }
 return $className;
 }
 public function getMetadataValue($name)
 {
 if (isset($this->{$name})) {
 return $this->{$name};
 }
 return null;
 }
 public function mapEmbedded(array $mapping)
 {
 $this->assertFieldNotMapped($mapping['fieldName']);
 if (!isset($mapping['class']) && $this->isTypedProperty($mapping['fieldName'])) {
 $type = $this->reflClass->getProperty($mapping['fieldName'])->getType();
 if ($type instanceof ReflectionNamedType) {
 $mapping['class'] = $type->getName();
 }
 }
 $this->embeddedClasses[$mapping['fieldName']] = ['class' => $this->fullyQualifiedClassName($mapping['class']), 'columnPrefix' => $mapping['columnPrefix'] ?? null, 'declaredField' => $mapping['declaredField'] ?? null, 'originalField' => $mapping['originalField'] ?? null];
 }
 public function inlineEmbeddable($property, ClassMetadataInfo $embeddable)
 {
 foreach ($embeddable->fieldMappings as $fieldMapping) {
 $fieldMapping['originalClass'] = $fieldMapping['originalClass'] ?? $embeddable->name;
 $fieldMapping['declaredField'] = isset($fieldMapping['declaredField']) ? $property . '.' . $fieldMapping['declaredField'] : $property;
 $fieldMapping['originalField'] = $fieldMapping['originalField'] ?? $fieldMapping['fieldName'];
 $fieldMapping['fieldName'] = $property . '.' . $fieldMapping['fieldName'];
 if (!empty($this->embeddedClasses[$property]['columnPrefix'])) {
 $fieldMapping['columnName'] = $this->embeddedClasses[$property]['columnPrefix'] . $fieldMapping['columnName'];
 } elseif ($this->embeddedClasses[$property]['columnPrefix'] !== \false) {
 $fieldMapping['columnName'] = $this->namingStrategy->embeddedFieldToColumnName($property, $fieldMapping['columnName'], $this->reflClass->name, $embeddable->reflClass->name);
 }
 $this->mapField($fieldMapping);
 }
 }
 private function assertFieldNotMapped(string $fieldName) : void
 {
 if (isset($this->fieldMappings[$fieldName]) || isset($this->associationMappings[$fieldName]) || isset($this->embeddedClasses[$fieldName])) {
 throw MappingException::duplicateFieldMapping($this->name, $fieldName);
 }
 }
 public function getSequenceName(AbstractPlatform $platform)
 {
 $sequencePrefix = $this->getSequencePrefix($platform);
 $columnName = $this->getSingleIdentifierColumnName();
 return $sequencePrefix . '_' . $columnName . '_seq';
 }
 public function getSequencePrefix(AbstractPlatform $platform)
 {
 $tableName = $this->getTableName();
 $sequencePrefix = $tableName;
 // Prepend the schema name to the table name if there is one
 $schemaName = $this->getSchemaName();
 if ($schemaName) {
 $sequencePrefix = $schemaName . '.' . $tableName;
 if (!$platform->supportsSchemas() && $platform->canEmulateSchemas()) {
 $sequencePrefix = $schemaName . '__' . $tableName;
 }
 }
 return $sequencePrefix;
 }
 private function assertMappingOrderBy(array $mapping) : void
 {
 if (isset($mapping['orderBy']) && !is_array($mapping['orderBy'])) {
 throw new InvalidArgumentException("'orderBy' is expected to be an array, not " . gettype($mapping['orderBy']));
 }
 }
 private function getAccessibleProperty(ReflectionService $reflService, string $class, string $field) : ?ReflectionProperty
 {
 $reflectionProperty = $reflService->getAccessibleProperty($class, $field);
 if ($reflectionProperty !== null && PHP_VERSION_ID >= 80100 && $reflectionProperty->isReadOnly()) {
 $reflectionProperty = new ReflectionReadonlyProperty($reflectionProperty);
 }
 return $reflectionProperty;
 }
}
