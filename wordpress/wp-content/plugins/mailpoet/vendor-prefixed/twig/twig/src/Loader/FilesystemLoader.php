<?php
namespace MailPoetVendor\Twig\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Source;
class FilesystemLoader implements LoaderInterface
{
 public const MAIN_NAMESPACE = '__main__';
 protected $paths = [];
 protected $cache = [];
 protected $errorCache = [];
 private $rootPath;
 public function __construct($paths = [], ?string $rootPath = null)
 {
 $this->rootPath = ($rootPath ?? \getcwd()) . \DIRECTORY_SEPARATOR;
 if (null !== $rootPath && \false !== ($realPath = \realpath($rootPath))) {
 $this->rootPath = $realPath . \DIRECTORY_SEPARATOR;
 }
 if ($paths) {
 $this->setPaths($paths);
 }
 }
 public function getPaths(string $namespace = self::MAIN_NAMESPACE) : array
 {
 return $this->paths[$namespace] ?? [];
 }
 public function getNamespaces() : array
 {
 return \array_keys($this->paths);
 }
 public function setPaths($paths, string $namespace = self::MAIN_NAMESPACE) : void
 {
 if (!\is_array($paths)) {
 $paths = [$paths];
 }
 $this->paths[$namespace] = [];
 foreach ($paths as $path) {
 $this->addPath($path, $namespace);
 }
 }
 public function addPath(string $path, string $namespace = self::MAIN_NAMESPACE) : void
 {
 // invalidate the cache
 $this->cache = $this->errorCache = [];
 $checkPath = $this->isAbsolutePath($path) ? $path : $this->rootPath . $path;
 if (!\is_dir($checkPath)) {
 throw new LoaderError(\sprintf('The "%s" directory does not exist ("%s").', $path, $checkPath));
 }
 $this->paths[$namespace][] = \rtrim($path, '/\\');
 }
 public function prependPath(string $path, string $namespace = self::MAIN_NAMESPACE) : void
 {
 // invalidate the cache
 $this->cache = $this->errorCache = [];
 $checkPath = $this->isAbsolutePath($path) ? $path : $this->rootPath . $path;
 if (!\is_dir($checkPath)) {
 throw new LoaderError(\sprintf('The "%s" directory does not exist ("%s").', $path, $checkPath));
 }
 $path = \rtrim($path, '/\\');
 if (!isset($this->paths[$namespace])) {
 $this->paths[$namespace][] = $path;
 } else {
 \array_unshift($this->paths[$namespace], $path);
 }
 }
 public function getSourceContext(string $name) : Source
 {
 if (null === ($path = $this->findTemplate($name))) {
 return new Source('', $name, '');
 }
 return new Source(\file_get_contents($path), $name, $path);
 }
 public function getCacheKey(string $name) : string
 {
 if (null === ($path = $this->findTemplate($name))) {
 return '';
 }
 $len = \strlen($this->rootPath);
 if (0 === \strncmp($this->rootPath, $path, $len)) {
 return \substr($path, $len);
 }
 return $path;
 }
 public function exists(string $name)
 {
 $name = $this->normalizeName($name);
 if (isset($this->cache[$name])) {
 return \true;
 }
 return null !== $this->findTemplate($name, \false);
 }
 public function isFresh(string $name, int $time) : bool
 {
 // false support to be removed in 3.0
 if (null === ($path = $this->findTemplate($name))) {
 return \false;
 }
 return \filemtime($path) < $time;
 }
 protected function findTemplate(string $name, bool $throw = \true)
 {
 $name = $this->normalizeName($name);
 if (isset($this->cache[$name])) {
 return $this->cache[$name];
 }
 if (isset($this->errorCache[$name])) {
 if (!$throw) {
 return null;
 }
 throw new LoaderError($this->errorCache[$name]);
 }
 try {
 [$namespace, $shortname] = $this->parseName($name);
 $this->validateName($shortname);
 } catch (LoaderError $e) {
 if (!$throw) {
 return null;
 }
 throw $e;
 }
 if (!isset($this->paths[$namespace])) {
 $this->errorCache[$name] = \sprintf('There are no registered paths for namespace "%s".', $namespace);
 if (!$throw) {
 return null;
 }
 throw new LoaderError($this->errorCache[$name]);
 }
 foreach ($this->paths[$namespace] as $path) {
 if (!$this->isAbsolutePath($path)) {
 $path = $this->rootPath . $path;
 }
 if (\is_file($path . '/' . $shortname)) {
 if (\false !== ($realpath = \realpath($path . '/' . $shortname))) {
 return $this->cache[$name] = $realpath;
 }
 return $this->cache[$name] = $path . '/' . $shortname;
 }
 }
 $this->errorCache[$name] = \sprintf('Unable to find template "%s" (looked into: %s).', $name, \implode(', ', $this->paths[$namespace]));
 if (!$throw) {
 return null;
 }
 throw new LoaderError($this->errorCache[$name]);
 }
 private function normalizeName(string $name) : string
 {
 return \preg_replace('#/{2,}#', '/', \str_replace('\\', '/', $name));
 }
 private function parseName(string $name, string $default = self::MAIN_NAMESPACE) : array
 {
 if (isset($name[0]) && '@' == $name[0]) {
 if (\false === ($pos = \strpos($name, '/'))) {
 throw new LoaderError(\sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
 }
 $namespace = \substr($name, 1, $pos - 1);
 $shortname = \substr($name, $pos + 1);
 return [$namespace, $shortname];
 }
 return [$default, $name];
 }
 private function validateName(string $name) : void
 {
 if (\str_contains($name, "\x00")) {
 throw new LoaderError('A template name cannot contain NUL bytes.');
 }
 $name = \ltrim($name, '/');
 $parts = \explode('/', $name);
 $level = 0;
 foreach ($parts as $part) {
 if ('..' === $part) {
 --$level;
 } elseif ('.' !== $part) {
 ++$level;
 }
 if ($level < 0) {
 throw new LoaderError(\sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
 }
 }
 }
 private function isAbsolutePath(string $file) : bool
 {
 return \strspn($file, '/\\', 0, 1) || \strlen($file) > 3 && \ctype_alpha($file[0]) && ':' === $file[1] && \strspn($file, '/\\', 2, 1) || null !== \parse_url($file, \PHP_URL_SCHEME);
 }
}
