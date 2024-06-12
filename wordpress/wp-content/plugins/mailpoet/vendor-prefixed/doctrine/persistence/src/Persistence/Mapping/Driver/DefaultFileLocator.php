<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\MappingException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function array_merge;
use function array_unique;
use function is_dir;
use function is_file;
use function str_replace;
use const DIRECTORY_SEPARATOR;
class DefaultFileLocator implements FileLocator
{
 protected $paths = [];
 protected $fileExtension;
 public function __construct($paths, $fileExtension = null)
 {
 $this->addPaths((array) $paths);
 $this->fileExtension = $fileExtension;
 }
 public function addPaths(array $paths)
 {
 $this->paths = array_unique(array_merge($this->paths, $paths));
 }
 public function getPaths()
 {
 return $this->paths;
 }
 public function getFileExtension()
 {
 return $this->fileExtension;
 }
 public function setFileExtension($fileExtension)
 {
 $this->fileExtension = $fileExtension;
 }
 public function findMappingFile($className)
 {
 $fileName = str_replace('\\', '.', $className) . $this->fileExtension;
 // Check whether file exists
 foreach ($this->paths as $path) {
 if (is_file($path . DIRECTORY_SEPARATOR . $fileName)) {
 return $path . DIRECTORY_SEPARATOR . $fileName;
 }
 }
 throw MappingException::mappingFileNotFound($className, $fileName);
 }
 public function getAllClassNames($globalBasename)
 {
 $classes = [];
 if ($this->paths) {
 foreach ($this->paths as $path) {
 if (!is_dir($path)) {
 throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
 }
 $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::LEAVES_ONLY);
 foreach ($iterator as $file) {
 $fileName = $file->getBasename($this->fileExtension);
 if ($fileName === $file->getBasename() || $fileName === $globalBasename) {
 continue;
 }
 // NOTE: All files found here means classes are not transient!
 $class = str_replace('.', '\\', $fileName);
 $classes[] = $class;
 }
 }
 }
 return $classes;
 }
 public function fileExists($className)
 {
 $fileName = str_replace('\\', '.', $className) . $this->fileExtension;
 // Check whether file exists
 foreach ((array) $this->paths as $path) {
 if (is_file($path . DIRECTORY_SEPARATOR . $fileName)) {
 return \true;
 }
 }
 return \false;
 }
}
