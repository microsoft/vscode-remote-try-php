<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
interface ObjectManagerAware
{
 public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata);
}
