<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
class YamlFilesLoader extends FilesLoader
{
 public function getFileLoaderInstance(string $file)
 {
 return new YamlFileLoader($file);
 }
}
