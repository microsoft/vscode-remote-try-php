<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
interface MappingDriver
{
 public function loadMetadataForClass($className, ClassMetadata $metadata);
 public function getAllClassNames();
 public function isTransient($className);
}
