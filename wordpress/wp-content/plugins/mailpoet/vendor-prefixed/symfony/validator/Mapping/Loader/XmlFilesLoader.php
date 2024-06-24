<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
class XmlFilesLoader extends FilesLoader
{
 public function getFileLoaderInstance(string $file)
 {
 return new XmlFileLoader($file);
 }
}
