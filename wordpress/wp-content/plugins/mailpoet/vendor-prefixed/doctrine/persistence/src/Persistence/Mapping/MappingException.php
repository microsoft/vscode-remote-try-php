<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use Exception;
use function implode;
use function sprintf;
class MappingException extends Exception
{
 public static function classNotFoundInNamespaces($className, $namespaces)
 {
 return new self(sprintf("The class '%s' was not found in the chain configured namespaces %s", $className, implode(', ', $namespaces)));
 }
 public static function pathRequired()
 {
 return new self('Specifying the paths to your entities is required ' . 'in the AnnotationDriver to retrieve all class names.');
 }
 public static function pathRequiredForDriver(string $driverClassName) : self
 {
 return new self(sprintf('Specifying the paths to your entities is required when using %s to retrieve all class names.', $driverClassName));
 }
 public static function fileMappingDriversRequireConfiguredDirectoryPath($path = null)
 {
 if (!empty($path)) {
 $path = '[' . $path . ']';
 }
 return new self(sprintf('File mapping drivers must have a valid directory path, ' . 'however the given path %s seems to be incorrect!', (string) $path));
 }
 public static function mappingFileNotFound($entityName, $fileName)
 {
 return new self(sprintf("No mapping file found named '%s' for class '%s'.", $fileName, $entityName));
 }
 public static function invalidMappingFile($entityName, $fileName)
 {
 return new self(sprintf("Invalid mapping file '%s' for class '%s'.", $fileName, $entityName));
 }
 public static function nonExistingClass($className)
 {
 return new self(sprintf("Class '%s' does not exist", $className));
 }
}
