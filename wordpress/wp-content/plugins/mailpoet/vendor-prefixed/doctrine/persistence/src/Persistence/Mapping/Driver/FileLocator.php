<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
interface FileLocator
{
 public function findMappingFile($className);
 public function getAllClassNames($globalBasename);
 public function fileExists($className);
 public function getPaths();
 public function getFileExtension();
}
