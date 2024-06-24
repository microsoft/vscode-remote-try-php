<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Mapping\MappingException;
use function array_keys;
use function assert;
use function spl_object_hash;
use function strpos;
class MappingDriverChain implements MappingDriver
{
 private $defaultDriver;
 private $drivers = [];
 public function getDefaultDriver()
 {
 return $this->defaultDriver;
 }
 public function setDefaultDriver(MappingDriver $driver)
 {
 $this->defaultDriver = $driver;
 }
 public function addDriver(MappingDriver $nestedDriver, $namespace)
 {
 $this->drivers[$namespace] = $nestedDriver;
 }
 public function getDrivers()
 {
 return $this->drivers;
 }
 public function loadMetadataForClass($className, ClassMetadata $metadata)
 {
 foreach ($this->drivers as $namespace => $driver) {
 assert($driver instanceof MappingDriver);
 if (strpos($className, $namespace) === 0) {
 $driver->loadMetadataForClass($className, $metadata);
 return;
 }
 }
 if ($this->defaultDriver !== null) {
 $this->defaultDriver->loadMetadataForClass($className, $metadata);
 return;
 }
 throw MappingException::classNotFoundInNamespaces($className, array_keys($this->drivers));
 }
 public function getAllClassNames()
 {
 $classNames = [];
 $driverClasses = [];
 foreach ($this->drivers as $namespace => $driver) {
 assert($driver instanceof MappingDriver);
 $oid = spl_object_hash($driver);
 if (!isset($driverClasses[$oid])) {
 $driverClasses[$oid] = $driver->getAllClassNames();
 }
 foreach ($driverClasses[$oid] as $className) {
 if (strpos($className, $namespace) !== 0) {
 continue;
 }
 $classNames[$className] = \true;
 }
 }
 if ($this->defaultDriver !== null) {
 foreach ($this->defaultDriver->getAllClassNames() as $className) {
 $classNames[$className] = \true;
 }
 }
 return array_keys($classNames);
 }
 public function isTransient($className)
 {
 foreach ($this->drivers as $namespace => $driver) {
 assert($driver instanceof MappingDriver);
 if (strpos($className, $namespace) === 0) {
 return $driver->isTransient($className);
 }
 }
 if ($this->defaultDriver !== null) {
 return $this->defaultDriver->isTransient($className);
 }
 return \true;
 }
}
