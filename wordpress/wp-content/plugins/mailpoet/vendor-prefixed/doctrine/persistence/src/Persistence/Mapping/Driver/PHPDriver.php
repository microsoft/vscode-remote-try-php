<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
class PHPDriver extends FileDriver
{
 protected $metadata;
 public function __construct($locator)
 {
 parent::__construct($locator, '.php');
 }
 public function loadMetadataForClass($className, ClassMetadata $metadata)
 {
 $this->metadata = $metadata;
 $this->loadMappingFile($this->locator->findMappingFile($className));
 }
 protected function loadMappingFile($file)
 {
 $metadata = $this->metadata;
 include $file;
 return [$metadata->getName() => $metadata];
 }
}
