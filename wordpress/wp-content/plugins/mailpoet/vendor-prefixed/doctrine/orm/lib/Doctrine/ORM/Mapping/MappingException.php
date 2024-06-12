<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use ReflectionException;
use ValueError;
use function array_keys;
use function array_map;
use function array_values;
use function get_debug_type;
use function get_parent_class;
use function implode;
use function sprintf;
class MappingException extends ORMException
{
 public static function pathRequired()
 {
 return new self('Specifying the paths to your entities is required ' . 'in the AnnotationDriver to retrieve all class names.');
 }
 public static function identifierRequired($entityName)
 {
 $parent = get_parent_class($entityName);
 if ($parent !== \false) {
 return new self(sprintf('No identifier/primary key specified for Entity "%s" sub class of "%s". Every Entity must have an identifier/primary key.', $entityName, $parent));
 }
 return new self(sprintf('No identifier/primary key specified for Entity "%s". Every Entity must have an identifier/primary key.', $entityName));
 }
 public static function invalidInheritanceType($entityName, $type)
 {
 return new self(sprintf("The inheritance type '%s' specified for '%s' does not exist.", $type, $entityName));
 }
 public static function generatorNotAllowedWithCompositeId()
 {
 return new self("Id generators can't be used with a composite id.");
 }
 public static function missingFieldName($entity)
 {
 return new self(sprintf("The field or association mapping misses the 'fieldName' attribute in entity '%s'.", $entity));
 }
 public static function missingTargetEntity($fieldName)
 {
 return new self(sprintf("The association mapping '%s' misses the 'targetEntity' attribute.", $fieldName));
 }
 public static function missingSourceEntity($fieldName)
 {
 return new self(sprintf("The association mapping '%s' misses the 'sourceEntity' attribute.", $fieldName));
 }
 public static function missingEmbeddedClass($fieldName)
 {
 return new self(sprintf("The embed mapping '%s' misses the 'class' attribute.", $fieldName));
 }
 public static function mappingFileNotFound($entityName, $fileName)
 {
 return new self(sprintf("No mapping file found named '%s' for class '%s'.", $fileName, $entityName));
 }
 public static function invalidOverrideFieldName($className, $fieldName)
 {
 return new self(sprintf("Invalid field override named '%s' for class '%s'.", $fieldName, $className));
 }
 public static function invalidOverrideFieldType($className, $fieldName)
 {
 return new self(sprintf("The column type of attribute '%s' on class '%s' could not be changed.", $fieldName, $className));
 }
 public static function mappingNotFound($className, $fieldName)
 {
 return new self(sprintf("No mapping found for field '%s' on class '%s'.", $fieldName, $className));
 }
 public static function queryNotFound($className, $queryName)
 {
 return new self(sprintf("No query found named '%s' on class '%s'.", $queryName, $className));
 }
 public static function resultMappingNotFound($className, $resultName)
 {
 return new self(sprintf("No result set mapping found named '%s' on class '%s'.", $resultName, $className));
 }
 public static function emptyQueryMapping($entity, $queryName)
 {
 return new self(sprintf('Query named "%s" in "%s" could not be empty.', $queryName, $entity));
 }
 public static function nameIsMandatoryForQueryMapping($className)
 {
 return new self(sprintf("Query name on entity class '%s' is not defined.", $className));
 }
 public static function missingQueryMapping($entity, $queryName)
 {
 return new self(sprintf('Query named "%s" in "%s requires a result class or result set mapping.', $queryName, $entity));
 }
 public static function missingResultSetMappingEntity($entity, $resultName)
 {
 return new self(sprintf('Result set mapping named "%s" in "%s requires a entity class name.', $resultName, $entity));
 }
 public static function missingResultSetMappingFieldName($entity, $resultName)
 {
 return new self(sprintf('Result set mapping named "%s" in "%s requires a field name.', $resultName, $entity));
 }
 public static function nameIsMandatoryForSqlResultSetMapping($className)
 {
 return new self(sprintf("Result set mapping name on entity class '%s' is not defined.", $className));
 }
 public static function oneToManyRequiresMappedBy(string $entityName, string $fieldName) : MappingException
 {
 return new self(sprintf("OneToMany mapping on entity '%s' field '%s' requires the 'mappedBy' attribute.", $entityName, $fieldName));
 }
 public static function joinTableRequired($fieldName)
 {
 return new self(sprintf("The mapping of field '%s' requires an the 'joinTable' attribute.", $fieldName));
 }
 public static function missingRequiredOption($field, $expectedOption, $hint = '')
 {
 $message = "The mapping of field '" . $field . "' is invalid: The option '" . $expectedOption . "' is required.";
 if (!empty($hint)) {
 $message .= ' (Hint: ' . $hint . ')';
 }
 return new self($message);
 }
 public static function invalidMapping($fieldName)
 {
 return new self(sprintf("The mapping of field '%s' is invalid.", $fieldName));
 }
 public static function reflectionFailure($entity, ReflectionException $previousException)
 {
 return new self(sprintf('An error occurred in %s', $entity), 0, $previousException);
 }
 public static function joinColumnMustPointToMappedField($className, $joinColumn)
 {
 return new self(sprintf('The column %s must be mapped to a field in class %s since it is referenced by a join column of another class.', $joinColumn, $className));
 }
 public static function classIsNotAValidEntityOrMappedSuperClass($className)
 {
 $parent = get_parent_class($className);
 if ($parent !== \false) {
 return new self(sprintf('Class "%s" sub class of "%s" is not a valid entity or mapped super class.', $className, $parent));
 }
 return new self(sprintf('Class "%s" is not a valid entity or mapped super class.', $className));
 }
 public static function propertyTypeIsRequired($className, $propertyName)
 {
 return new self(sprintf("The attribute 'type' is required for the column description of property %s::\$%s.", $className, $propertyName));
 }
 public static function duplicateFieldMapping($entity, $fieldName)
 {
 return new self(sprintf('Property "%s" in "%s" was already declared, but it must be declared only once', $fieldName, $entity));
 }
 public static function duplicateAssociationMapping($entity, $fieldName)
 {
 return new self(sprintf('Property "%s" in "%s" was already declared, but it must be declared only once', $fieldName, $entity));
 }
 public static function duplicateQueryMapping($entity, $queryName)
 {
 return new self(sprintf('Query named "%s" in "%s" was already declared, but it must be declared only once', $queryName, $entity));
 }
 public static function duplicateResultSetMapping($entity, $resultName)
 {
 return new self(sprintf('Result set mapping named "%s" in "%s" was already declared, but it must be declared only once', $resultName, $entity));
 }
 public static function singleIdNotAllowedOnCompositePrimaryKey($entity)
 {
 return new self('Single id is not allowed on composite primary key in entity ' . $entity);
 }
 public static function noIdDefined($entity)
 {
 return new self('No ID defined for entity ' . $entity);
 }
 public static function unsupportedOptimisticLockingType($entity, $fieldName, $unsupportedType)
 {
 return new self(sprintf('Locking type "%s" (specified in "%s", field "%s") is not supported by Doctrine.', $unsupportedType, $entity, $fieldName));
 }
 public static function fileMappingDriversRequireConfiguredDirectoryPath($path = null)
 {
 if (!empty($path)) {
 $path = '[' . $path . ']';
 }
 return new self('File mapping drivers must have a valid directory path, ' . 'however the given path ' . $path . ' seems to be incorrect!');
 }
 public static function invalidClassInDiscriminatorMap($className, $owningClass)
 {
 return new self(sprintf("Entity class '%s' used in the discriminator map of class '%s' " . 'does not exist.', $className, $owningClass));
 }
 public static function duplicateDiscriminatorEntry($className, array $entries, array $map)
 {
 return new self('The entries ' . implode(', ', $entries) . " in discriminator map of class '" . $className . "' is duplicated. " . 'If the discriminator map is automatically generated you have to convert it to an explicit discriminator map now. ' . 'The entries of the current map are: @DiscriminatorMap({' . implode(', ', array_map(static function ($a, $b) {
 return sprintf("'%s': '%s'", $a, $b);
 }, array_keys($map), array_values($map))) . '})');
 }
 public static function missingDiscriminatorMap($className)
 {
 return new self(sprintf("Entity class '%s' is using inheritance but no discriminator map was defined.", $className));
 }
 public static function missingDiscriminatorColumn($className)
 {
 return new self(sprintf("Entity class '%s' is using inheritance but no discriminator column was defined.", $className));
 }
 public static function invalidDiscriminatorColumnType($className, $type)
 {
 return new self(sprintf("Discriminator column type on entity class '%s' is not allowed to be '%s'. 'string' or 'integer' type variables are suggested!", $className, $type));
 }
 public static function nameIsMandatoryForDiscriminatorColumns($className)
 {
 return new self(sprintf("Discriminator column name on entity class '%s' is not defined.", $className));
 }
 public static function cannotVersionIdField($className, $fieldName)
 {
 return new self(sprintf("Setting Id field '%s' as versionable in entity class '%s' is not supported.", $fieldName, $className));
 }
 public static function sqlConversionNotAllowedForIdentifiers($className, $fieldName, $type)
 {
 return new self(sprintf("It is not possible to set id field '%s' to type '%s' in entity class '%s'. The type '%s' requires conversion SQL which is not allowed for identifiers.", $fieldName, $type, $className, $type));
 }
 public static function duplicateColumnName($className, $columnName)
 {
 return new self("Duplicate definition of column '" . $columnName . "' on entity '" . $className . "' in a field or discriminator column mapping.");
 }
 public static function illegalToManyAssociationOnMappedSuperclass($className, $field)
 {
 return new self("It is illegal to put an inverse side one-to-many or many-to-many association on mapped superclass '" . $className . '#' . $field . "'.");
 }
 public static function cannotMapCompositePrimaryKeyEntitiesAsForeignId($className, $targetEntity, $targetField)
 {
 return new self("It is not possible to map entity '" . $className . "' with a composite primary key " . "as part of the primary key of another entity '" . $targetEntity . '#' . $targetField . "'.");
 }
 public static function noSingleAssociationJoinColumnFound($className, $field)
 {
 return new self(sprintf("'%s#%s' is not an association with a single join column.", $className, $field));
 }
 public static function noFieldNameFoundForColumn($className, $column)
 {
 return new self(sprintf("Cannot find a field on '%s' that is mapped to column '%s'. Either the " . 'field does not exist or an association exists but it has multiple join columns.', $className, $column));
 }
 public static function illegalOrphanRemovalOnIdentifierAssociation($className, $field)
 {
 return new self(sprintf("The orphan removal option is not allowed on an association that is part of the identifier in '%s#%s'.", $className, $field));
 }
 public static function illegalOrphanRemoval($className, $field)
 {
 return new self('Orphan removal is only allowed on one-to-one and one-to-many ' . 'associations, but ' . $className . '#' . $field . ' is not.');
 }
 public static function illegalInverseIdentifierAssociation($className, $field)
 {
 return new self(sprintf("An inverse association is not allowed to be identifier in '%s#%s'.", $className, $field));
 }
 public static function illegalToManyIdentifierAssociation($className, $field)
 {
 return new self(sprintf("Many-to-many or one-to-many associations are not allowed to be identifier in '%s#%s'.", $className, $field));
 }
 public static function noInheritanceOnMappedSuperClass($className)
 {
 return new self("It is not supported to define inheritance information on a mapped superclass '" . $className . "'.");
 }
 public static function mappedClassNotPartOfDiscriminatorMap($className, $rootClassName)
 {
 return new self("Entity '" . $className . "' has to be part of the discriminator map of '" . $rootClassName . "' " . "to be properly mapped in the inheritance hierarchy. Alternatively you can make '" . $className . "' an abstract class " . 'to avoid this exception from occurring.');
 }
 public static function lifecycleCallbackMethodNotFound($className, $methodName)
 {
 return new self("Entity '" . $className . "' has no method '" . $methodName . "' to be registered as lifecycle callback.");
 }
 public static function entityListenerClassNotFound($listenerName, $className)
 {
 return new self(sprintf('Entity Listener "%s" declared on "%s" not found.', $listenerName, $className));
 }
 public static function entityListenerMethodNotFound($listenerName, $methodName, $className)
 {
 return new self(sprintf('Entity Listener "%s" declared on "%s" has no method "%s".', $listenerName, $className, $methodName));
 }
 public static function duplicateEntityListener($listenerName, $methodName, $className)
 {
 return new self(sprintf('Entity Listener "%s#%s()" in "%s" was already declared, but it must be declared only once.', $listenerName, $methodName, $className));
 }
 public static function invalidFetchMode($className, $annotation)
 {
 return new self("Entity '" . $className . "' has a mapping with invalid fetch mode '" . $annotation . "'");
 }
 public static function invalidGeneratedMode(string $annotation) : MappingException
 {
 return new self("Invalid generated mode '" . $annotation . "'");
 }
 public static function compositeKeyAssignedIdGeneratorRequired($className)
 {
 return new self("Entity '" . $className . "' has a composite identifier but uses an ID generator other than manually assigning (Identity, Sequence). This is not supported.");
 }
 public static function invalidTargetEntityClass($targetEntity, $sourceEntity, $associationName)
 {
 return new self('The target-entity ' . $targetEntity . " cannot be found in '" . $sourceEntity . '#' . $associationName . "'.");
 }
 public static function invalidCascadeOption(array $cascades, $className, $propertyName)
 {
 $cascades = implode(', ', array_map(static function ($e) {
 return "'" . $e . "'";
 }, $cascades));
 return new self(sprintf("You have specified invalid cascade options for %s::\$%s: %s; available options: 'remove', 'persist', 'refresh', 'merge', and 'detach'", $className, $propertyName, $cascades));
 }
 public static function missingSequenceName($className)
 {
 return new self(sprintf('Missing "sequenceName" attribute for sequence id generator definition on class "%s".', $className));
 }
 public static function infiniteEmbeddableNesting($className, $propertyName)
 {
 return new self(sprintf('Infinite nesting detected for embedded property %s::%s. ' . 'You cannot embed an embeddable from the same type inside an embeddable.', $className, $propertyName));
 }
 public static function illegalOverrideOfInheritedProperty($className, $propertyName)
 {
 return new self(sprintf('Override for %s::%s is only allowed for attributes/associations ' . 'declared on a mapped superclass or a trait.', $className, $propertyName));
 }
 public static function invalidIndexConfiguration($className, $indexName)
 {
 return new self(sprintf('Index %s for entity %s should contain columns or fields values, but not both.', $indexName, $className));
 }
 public static function invalidUniqueConstraintConfiguration($className, $indexName)
 {
 return new self(sprintf('Unique constraint %s for entity %s should contain columns or fields values, but not both.', $indexName, $className));
 }
 public static function invalidOverrideType(string $expectdType, $givenValue) : self
 {
 return new self(sprintf('Expected %s, but %s was given.', $expectdType, get_debug_type($givenValue)));
 }
 public static function enumsRequirePhp81(string $className, string $fieldName) : self
 {
 return new self(sprintf('Enum types require PHP 8.1 in %s::$%s', $className, $fieldName));
 }
 public static function nonEnumTypeMapped(string $className, string $fieldName, string $enumType) : self
 {
 return new self(sprintf('Attempting to map non-enum type %s as enum in entity %s::$%s', $enumType, $className, $fieldName));
 }
 public static function invalidEnumValue(string $className, string $fieldName, string $value, string $enumType, ValueError $previous) : self
 {
 return new self(sprintf(<<<'EXCEPTION'
Context: Trying to hydrate enum property "%s::$%s"
Problem: Case "%s" is not listed in enum "%s"
Solution: Either add the case to the enum type or migrate the database column to use another case of the enum
EXCEPTION
, $className, $fieldName, $value, $enumType), 0, $previous);
 }
}
