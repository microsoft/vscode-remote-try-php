<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache as CacheDriver;
use MailPoetVendor\Doctrine\Persistence\ObjectRepository;
use Exception;
use function get_debug_type;
use function implode;
use function sprintf;
class ORMException extends Exception
{
 public static function missingMappingDriverImpl()
 {
 return new self("It's a requirement to specify a Metadata Driver and pass it " . 'to Doctrine\\ORM\\Configuration::setMetadataDriverImpl().');
 }
 public static function namedQueryNotFound($queryName)
 {
 return new self('Could not find a named query by the name "' . $queryName . '"');
 }
 public static function namedNativeQueryNotFound($nativeQueryName)
 {
 return new self('Could not find a named native query by the name "' . $nativeQueryName . '"');
 }
 public static function unrecognizedField($field)
 {
 return new self(sprintf('Unrecognized field: %s', $field));
 }
 public static function unexpectedAssociationValue($class, $association, $given, $expected)
 {
 return new self(sprintf('Found entity of type %s on association %s#%s, but expecting %s', $given, $class, $association, $expected));
 }
 public static function invalidOrientation($className, $field)
 {
 return new self('Invalid order by orientation specified for ' . $className . '#' . $field);
 }
 public static function entityManagerClosed()
 {
 return new self('The EntityManager is closed.');
 }
 public static function invalidHydrationMode($mode)
 {
 return new self(sprintf("'%s' is an invalid hydration mode.", $mode));
 }
 public static function mismatchedEventManager()
 {
 return new self('Cannot use different EventManager instances for EntityManager and Connection.');
 }
 public static function findByRequiresParameter($methodName)
 {
 return new self("You need to pass a parameter to '" . $methodName . "'");
 }
 public static function invalidMagicCall($entityName, $fieldName, $method)
 {
 return new self("Entity '" . $entityName . "' has no field '" . $fieldName . "'. " . "You can therefore not call '" . $method . "' on the entities' repository");
 }
 public static function invalidFindByInverseAssociation($entityName, $associationFieldName)
 {
 return new self("You cannot search for the association field '" . $entityName . '#' . $associationFieldName . "', " . 'because it is the inverse side of an association. Find methods only work on owning side associations.');
 }
 public static function invalidResultCacheDriver()
 {
 return new self('Invalid result cache driver; it must implement Doctrine\\Common\\Cache\\Cache.');
 }
 public static function notSupported()
 {
 return new self('This behaviour is (currently) not supported by Doctrine 2');
 }
 public static function queryCacheNotConfigured()
 {
 return new self('Query Cache is not configured.');
 }
 public static function metadataCacheNotConfigured()
 {
 return new self('Class Metadata Cache is not configured.');
 }
 public static function queryCacheUsesNonPersistentCache(CacheDriver $cache)
 {
 return new self('Query Cache uses a non-persistent cache driver, ' . get_debug_type($cache) . '.');
 }
 public static function metadataCacheUsesNonPersistentCache(CacheDriver $cache)
 {
 return new self('Metadata Cache uses a non-persistent cache driver, ' . get_debug_type($cache) . '.');
 }
 public static function proxyClassesAlwaysRegenerating()
 {
 return new self('Proxy Classes are always regenerating.');
 }
 public static function unknownEntityNamespace($entityNamespaceAlias)
 {
 return new self(sprintf("Unknown Entity namespace alias '%s'.", $entityNamespaceAlias));
 }
 public static function invalidEntityRepository($className)
 {
 return new self(sprintf("Invalid repository class '%s'. It must be a %s.", $className, ObjectRepository::class));
 }
 public static function missingIdentifierField($className, $fieldName)
 {
 return new self(sprintf('The identifier %s is missing for a query of %s', $fieldName, $className));
 }
 public static function unrecognizedIdentifierFields($className, $fieldNames)
 {
 return new self("Unrecognized identifier fields: '" . implode("', '", $fieldNames) . "' " . "are not present on class '" . $className . "'.");
 }
 public static function cantUseInOperatorOnCompositeKeys()
 {
 return new self("Can't use IN operator on entities that have composite keys.");
 }
}
