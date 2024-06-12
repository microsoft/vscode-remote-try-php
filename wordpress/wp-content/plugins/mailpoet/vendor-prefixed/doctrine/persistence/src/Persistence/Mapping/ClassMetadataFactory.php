<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
interface ClassMetadataFactory
{
 public function getAllMetadata();
 public function getMetadataFor($className);
 public function hasMetadataFor($className);
 public function setMetadataFor($className, $class);
 public function isTransient($className);
}
