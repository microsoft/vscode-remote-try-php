<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Mapping\MappingException;
use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function is_file;
use function str_replace;
abstract class FileDriver implements MappingDriver
{
 protected $locator;
 protected $classCache;
 protected $globalBasename;
 public function __construct($locator, $fileExtension = null)
 {
 if ($locator instanceof FileLocator) {
 $this->locator = $locator;
 } else {
 $this->locator = new DefaultFileLocator((array) $locator, $fileExtension);
 }
 }
 public function setGlobalBasename($file)
 {
 $this->globalBasename = $file;
 }
 public function getGlobalBasename()
 {
 return $this->globalBasename;
 }
 public function getElement($className)
 {
 if ($this->classCache === null) {
 $this->initialize();
 }
 if (isset($this->classCache[$className])) {
 return $this->classCache[$className];
 }
 $result = $this->loadMappingFile($this->locator->findMappingFile($className));
 if (!isset($result[$className])) {
 throw MappingException::invalidMappingFile($className, str_replace('\\', '.', $className) . $this->locator->getFileExtension());
 }
 $this->classCache[$className] = $result[$className];
 return $result[$className];
 }
 public function isTransient($className)
 {
 if ($this->classCache === null) {
 $this->initialize();
 }
 if (isset($this->classCache[$className])) {
 return \false;
 }
 return !$this->locator->fileExists($className);
 }
 public function getAllClassNames()
 {
 if ($this->classCache === null) {
 $this->initialize();
 }
 if (!$this->classCache) {
 return (array) $this->locator->getAllClassNames($this->globalBasename);
 }
 return array_values(array_unique(array_merge(array_keys($this->classCache), (array) $this->locator->getAllClassNames($this->globalBasename))));
 }
 protected abstract function loadMappingFile($file);
 protected function initialize()
 {
 $this->classCache = [];
 if ($this->globalBasename === null) {
 return;
 }
 foreach ($this->locator->getPaths() as $path) {
 $file = $path . '/' . $this->globalBasename . $this->locator->getFileExtension();
 if (!is_file($file)) {
 continue;
 }
 $this->classCache = array_merge($this->classCache, $this->loadMappingFile($file));
 }
 }
 public function getLocator()
 {
 return $this->locator;
 }
 public function setLocator(FileLocator $locator)
 {
 $this->locator = $locator;
 }
}
