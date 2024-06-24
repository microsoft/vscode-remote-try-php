<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\MappingException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;
use function array_merge;
use function array_unique;
use function assert;
use function get_declared_classes;
use function in_array;
use function is_dir;
use function preg_match;
use function preg_quote;
use function realpath;
use function str_replace;
use function strpos;
trait ColocatedMappingDriver
{
 protected $paths = [];
 protected $excludePaths = [];
 protected $fileExtension = '.php';
 protected $classNames;
 public function addPaths(array $paths)
 {
 $this->paths = array_unique(array_merge($this->paths, $paths));
 }
 public function getPaths()
 {
 return $this->paths;
 }
 public function addExcludePaths(array $paths)
 {
 $this->excludePaths = array_unique(array_merge($this->excludePaths, $paths));
 }
 public function getExcludePaths()
 {
 return $this->excludePaths;
 }
 public function getFileExtension()
 {
 return $this->fileExtension;
 }
 public function setFileExtension(string $fileExtension)
 {
 $this->fileExtension = $fileExtension;
 }
 public abstract function isTransient($className);
 public function getAllClassNames()
 {
 if ($this->classNames !== null) {
 return $this->classNames;
 }
 if (!$this->paths) {
 throw MappingException::pathRequiredForDriver(static::class);
 }
 $classes = [];
 $includedFiles = [];
 foreach ($this->paths as $path) {
 if (!is_dir($path)) {
 throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
 }
 $iterator = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY), '/^.+' . preg_quote($this->fileExtension) . '$/i', RecursiveRegexIterator::GET_MATCH);
 foreach ($iterator as $file) {
 $sourceFile = $file[0];
 if (preg_match('(^phar:)i', $sourceFile) === 0) {
 $sourceFile = realpath($sourceFile);
 }
 foreach ($this->excludePaths as $excludePath) {
 $realExcludePath = realpath($excludePath);
 assert($realExcludePath !== \false);
 $exclude = str_replace('\\', '/', $realExcludePath);
 $current = str_replace('\\', '/', $sourceFile);
 if (strpos($current, $exclude) !== \false) {
 continue 2;
 }
 }
 require_once $sourceFile;
 $includedFiles[] = $sourceFile;
 }
 }
 $declared = get_declared_classes();
 foreach ($declared as $className) {
 $rc = new ReflectionClass($className);
 $sourceFile = $rc->getFileName();
 if (!in_array($sourceFile, $includedFiles) || $this->isTransient($className)) {
 continue;
 }
 $classes[] = $className;
 }
 $this->classNames = $classes;
 return $classes;
 }
}
