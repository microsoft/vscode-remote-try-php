<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use MailPoetVendor\Doctrine\DBAL\Driver\ResultStatement;
use MailPoetVendor\Doctrine\DBAL\ForwardCompatibility\Result as ForwardCompatibilityResult;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Events;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\ORM\Tools\Pagination\LimitSubqueryWalker;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use Generator;
use LogicException;
use ReflectionClass;
use TypeError;
use function array_map;
use function array_merge;
use function count;
use function end;
use function get_debug_type;
use function in_array;
use function is_array;
use function sprintf;
abstract class AbstractHydrator
{
 protected $_rsm;
 protected $_em;
 protected $_platform;
 protected $_uow;
 protected $_metadataCache = [];
 protected $_cache = [];
 protected $_stmt;
 protected $_hints = [];
 public function __construct(EntityManagerInterface $em)
 {
 $this->_em = $em;
 $this->_platform = $em->getConnection()->getDatabasePlatform();
 $this->_uow = $em->getUnitOfWork();
 }
 public function iterate($stmt, $resultSetMapping, array $hints = [])
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8463', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use toIterable() instead.', __METHOD__);
 $this->_stmt = $stmt instanceof ResultStatement ? ForwardCompatibilityResult::ensure($stmt) : $stmt;
 $this->_rsm = $resultSetMapping;
 $this->_hints = $hints;
 $evm = $this->_em->getEventManager();
 $evm->addEventListener([Events::onClear], $this);
 $this->prepare();
 return new IterableResult($this);
 }
 public function toIterable($stmt, ResultSetMapping $resultSetMapping, array $hints = []) : iterable
 {
 if (!$stmt instanceof Result) {
 if (!$stmt instanceof ResultStatement) {
 throw new TypeError(sprintf('%s: Expected parameter $stmt to be an instance of %s or %s, got %s', __METHOD__, Result::class, ResultStatement::class, get_debug_type($stmt)));
 }
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/8796', '%s: Passing a result as $stmt that does not implement %s is deprecated and will cause a TypeError on 3.0', __METHOD__, Result::class);
 $stmt = ForwardCompatibilityResult::ensure($stmt);
 }
 $this->_stmt = $stmt;
 $this->_rsm = $resultSetMapping;
 $this->_hints = $hints;
 $evm = $this->_em->getEventManager();
 $evm->addEventListener([Events::onClear], $this);
 $this->prepare();
 while (\true) {
 $row = $this->statement()->fetchAssociative();
 if ($row === \false) {
 $this->cleanup();
 break;
 }
 $result = [];
 $this->hydrateRowData($row, $result);
 $this->cleanupAfterRowIteration();
 if (count($result) === 1) {
 if (count($resultSetMapping->indexByMap) === 0) {
 (yield end($result));
 } else {
 yield from $result;
 }
 } else {
 (yield $result);
 }
 }
 }
 protected final function statement() : Result
 {
 if ($this->_stmt === null) {
 throw new LogicException('Uninitialized _stmt property');
 }
 return $this->_stmt;
 }
 protected final function resultSetMapping() : ResultSetMapping
 {
 if ($this->_rsm === null) {
 throw new LogicException('Uninitialized _rsm property');
 }
 return $this->_rsm;
 }
 public function hydrateAll($stmt, $resultSetMapping, array $hints = [])
 {
 if (!$stmt instanceof Result) {
 if (!$stmt instanceof ResultStatement) {
 throw new TypeError(sprintf('%s: Expected parameter $stmt to be an instance of %s or %s, got %s', __METHOD__, Result::class, ResultStatement::class, get_debug_type($stmt)));
 }
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/8796', '%s: Passing a result as $stmt that does not implement %s is deprecated and will cause a TypeError on 3.0', __METHOD__, Result::class);
 $stmt = ForwardCompatibilityResult::ensure($stmt);
 }
 $this->_stmt = $stmt;
 $this->_rsm = $resultSetMapping;
 $this->_hints = $hints;
 $this->_em->getEventManager()->addEventListener([Events::onClear], $this);
 $this->prepare();
 try {
 $result = $this->hydrateAllData();
 } finally {
 $this->cleanup();
 }
 return $result;
 }
 public function hydrateRow()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/orm', 'https://github.com/doctrine/orm/pull/9072', '%s is deprecated.', __METHOD__);
 $row = $this->statement()->fetchAssociative();
 if ($row === \false) {
 $this->cleanup();
 return \false;
 }
 $result = [];
 $this->hydrateRowData($row, $result);
 return $result;
 }
 public function onClear($eventArgs)
 {
 }
 protected function prepare()
 {
 }
 protected function cleanup()
 {
 $this->statement()->free();
 $this->_stmt = null;
 $this->_rsm = null;
 $this->_cache = [];
 $this->_metadataCache = [];
 $this->_em->getEventManager()->removeEventListener([Events::onClear], $this);
 }
 protected function cleanupAfterRowIteration() : void
 {
 }
 protected function hydrateRowData(array $row, array &$result)
 {
 throw new HydrationException('hydrateRowData() not implemented by this hydrator.');
 }
 protected abstract function hydrateAllData();
 protected function gatherRowData(array $data, array &$id, array &$nonemptyComponents)
 {
 $rowData = ['data' => []];
 foreach ($data as $key => $value) {
 $cacheKeyInfo = $this->hydrateColumnInfo($key);
 if ($cacheKeyInfo === null) {
 continue;
 }
 $fieldName = $cacheKeyInfo['fieldName'];
 switch (\true) {
 case isset($cacheKeyInfo['isNewObjectParameter']):
 $argIndex = $cacheKeyInfo['argIndex'];
 $objIndex = $cacheKeyInfo['objIndex'];
 $type = $cacheKeyInfo['type'];
 $value = $type->convertToPHPValue($value, $this->_platform);
 if ($value !== null && isset($cacheKeyInfo['enumType'])) {
 $value = $this->buildEnum($value, $cacheKeyInfo['enumType']);
 }
 $rowData['newObjects'][$objIndex]['class'] = $cacheKeyInfo['class'];
 $rowData['newObjects'][$objIndex]['args'][$argIndex] = $value;
 break;
 case isset($cacheKeyInfo['isScalar']):
 $type = $cacheKeyInfo['type'];
 $value = $type->convertToPHPValue($value, $this->_platform);
 if ($value !== null && isset($cacheKeyInfo['enumType'])) {
 $value = $this->buildEnum($value, $cacheKeyInfo['enumType']);
 }
 $rowData['scalars'][$fieldName] = $value;
 break;
 //case (isset($cacheKeyInfo['isMetaColumn'])):
 default:
 $dqlAlias = $cacheKeyInfo['dqlAlias'];
 $type = $cacheKeyInfo['type'];
 // If there are field name collisions in the child class, then we need
 // to only hydrate if we are looking at the correct discriminator value
 if (isset($cacheKeyInfo['discriminatorColumn'], $data[$cacheKeyInfo['discriminatorColumn']]) && !in_array((string) $data[$cacheKeyInfo['discriminatorColumn']], $cacheKeyInfo['discriminatorValues'], \true)) {
 break;
 }
 // in an inheritance hierarchy the same field could be defined several times.
 // We overwrite this value so long we don't have a non-null value, that value we keep.
 // Per definition it cannot be that a field is defined several times and has several values.
 if (isset($rowData['data'][$dqlAlias][$fieldName])) {
 break;
 }
 $rowData['data'][$dqlAlias][$fieldName] = $type ? $type->convertToPHPValue($value, $this->_platform) : $value;
 if ($rowData['data'][$dqlAlias][$fieldName] !== null && isset($cacheKeyInfo['enumType'])) {
 $rowData['data'][$dqlAlias][$fieldName] = $this->buildEnum($rowData['data'][$dqlAlias][$fieldName], $cacheKeyInfo['enumType']);
 }
 if ($cacheKeyInfo['isIdentifier'] && $value !== null) {
 $id[$dqlAlias] .= '|' . $value;
 $nonemptyComponents[$dqlAlias] = \true;
 }
 break;
 }
 }
 return $rowData;
 }
 protected function gatherScalarRowData(&$data)
 {
 $rowData = [];
 foreach ($data as $key => $value) {
 $cacheKeyInfo = $this->hydrateColumnInfo($key);
 if ($cacheKeyInfo === null) {
 continue;
 }
 $fieldName = $cacheKeyInfo['fieldName'];
 // WARNING: BC break! We know this is the desired behavior to type convert values, but this
 // erroneous behavior exists since 2.0 and we're forced to keep compatibility.
 if (!isset($cacheKeyInfo['isScalar'])) {
 $type = $cacheKeyInfo['type'];
 $value = $type ? $type->convertToPHPValue($value, $this->_platform) : $value;
 $fieldName = $cacheKeyInfo['dqlAlias'] . '_' . $fieldName;
 }
 $rowData[$fieldName] = $value;
 }
 return $rowData;
 }
 protected function hydrateColumnInfo($key)
 {
 if (isset($this->_cache[$key])) {
 return $this->_cache[$key];
 }
 switch (\true) {
 // NOTE: Most of the times it's a field mapping, so keep it first!!!
 case isset($this->_rsm->fieldMappings[$key]):
 $classMetadata = $this->getClassMetadata($this->_rsm->declaringClasses[$key]);
 $fieldName = $this->_rsm->fieldMappings[$key];
 $fieldMapping = $classMetadata->fieldMappings[$fieldName];
 $ownerMap = $this->_rsm->columnOwnerMap[$key];
 $columnInfo = ['isIdentifier' => in_array($fieldName, $classMetadata->identifier, \true), 'fieldName' => $fieldName, 'type' => Type::getType($fieldMapping['type']), 'dqlAlias' => $ownerMap, 'enumType' => $this->_rsm->enumMappings[$key] ?? null];
 // the current discriminator value must be saved in order to disambiguate fields hydration,
 // should there be field name collisions
 if ($classMetadata->parentClasses && isset($this->_rsm->discriminatorColumns[$ownerMap])) {
 return $this->_cache[$key] = array_merge($columnInfo, ['discriminatorColumn' => $this->_rsm->discriminatorColumns[$ownerMap], 'discriminatorValue' => $classMetadata->discriminatorValue, 'discriminatorValues' => $this->getDiscriminatorValues($classMetadata)]);
 }
 return $this->_cache[$key] = $columnInfo;
 case isset($this->_rsm->newObjectMappings[$key]):
 // WARNING: A NEW object is also a scalar, so it must be declared before!
 $mapping = $this->_rsm->newObjectMappings[$key];
 return $this->_cache[$key] = ['isScalar' => \true, 'isNewObjectParameter' => \true, 'fieldName' => $this->_rsm->scalarMappings[$key], 'type' => Type::getType($this->_rsm->typeMappings[$key]), 'argIndex' => $mapping['argIndex'], 'objIndex' => $mapping['objIndex'], 'class' => new ReflectionClass($mapping['className']), 'enumType' => $this->_rsm->enumMappings[$key] ?? null];
 case isset($this->_rsm->scalarMappings[$key], $this->_hints[LimitSubqueryWalker::FORCE_DBAL_TYPE_CONVERSION]):
 return $this->_cache[$key] = ['fieldName' => $this->_rsm->scalarMappings[$key], 'type' => Type::getType($this->_rsm->typeMappings[$key]), 'dqlAlias' => '', 'enumType' => $this->_rsm->enumMappings[$key] ?? null];
 case isset($this->_rsm->scalarMappings[$key]):
 return $this->_cache[$key] = ['isScalar' => \true, 'fieldName' => $this->_rsm->scalarMappings[$key], 'type' => Type::getType($this->_rsm->typeMappings[$key]), 'enumType' => $this->_rsm->enumMappings[$key] ?? null];
 case isset($this->_rsm->metaMappings[$key]):
 // Meta column (has meaning in relational schema only, i.e. foreign keys or discriminator columns).
 $fieldName = $this->_rsm->metaMappings[$key];
 $dqlAlias = $this->_rsm->columnOwnerMap[$key];
 $type = isset($this->_rsm->typeMappings[$key]) ? Type::getType($this->_rsm->typeMappings[$key]) : null;
 // Cache metadata fetch
 $this->getClassMetadata($this->_rsm->aliasMap[$dqlAlias]);
 return $this->_cache[$key] = ['isIdentifier' => isset($this->_rsm->isIdentifierColumn[$dqlAlias][$key]), 'isMetaColumn' => \true, 'fieldName' => $fieldName, 'type' => $type, 'dqlAlias' => $dqlAlias, 'enumType' => $this->_rsm->enumMappings[$key] ?? null];
 }
 // this column is a left over, maybe from a LIMIT query hack for example in Oracle or DB2
 // maybe from an additional column that has not been defined in a NativeQuery ResultSetMapping.
 return null;
 }
 private function getDiscriminatorValues(ClassMetadata $classMetadata) : array
 {
 $values = array_map(function (string $subClass) : string {
 return (string) $this->getClassMetadata($subClass)->discriminatorValue;
 }, $classMetadata->subClasses);
 $values[] = (string) $classMetadata->discriminatorValue;
 return $values;
 }
 protected function getClassMetadata($className)
 {
 if (!isset($this->_metadataCache[$className])) {
 $this->_metadataCache[$className] = $this->_em->getClassMetadata($className);
 }
 return $this->_metadataCache[$className];
 }
 protected function registerManaged(ClassMetadata $class, $entity, array $data)
 {
 if ($class->isIdentifierComposite) {
 $id = [];
 foreach ($class->identifier as $fieldName) {
 $id[$fieldName] = isset($class->associationMappings[$fieldName]) ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']] : $data[$fieldName];
 }
 } else {
 $fieldName = $class->identifier[0];
 $id = [$fieldName => isset($class->associationMappings[$fieldName]) ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']] : $data[$fieldName]];
 }
 $this->_em->getUnitOfWork()->registerManaged($entity, $id, $data);
 }
 protected final function buildEnum($value, string $enumType)
 {
 if (is_array($value)) {
 return array_map(static function ($value) use($enumType) : BackedEnum {
 return $enumType::from($value);
 }, $value);
 }
 return $enumType::from($value);
 }
}
