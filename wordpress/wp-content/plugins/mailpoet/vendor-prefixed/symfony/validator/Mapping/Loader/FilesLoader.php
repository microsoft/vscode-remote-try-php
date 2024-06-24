<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
abstract class FilesLoader extends LoaderChain
{
 public function __construct(array $paths)
 {
 parent::__construct($this->getFileLoaders($paths));
 }
 protected function getFileLoaders(array $paths)
 {
 $loaders = [];
 foreach ($paths as $path) {
 $loaders[] = $this->getFileLoaderInstance($path);
 }
 return $loaders;
 }
 protected abstract function getFileLoaderInstance(string $path);
}
