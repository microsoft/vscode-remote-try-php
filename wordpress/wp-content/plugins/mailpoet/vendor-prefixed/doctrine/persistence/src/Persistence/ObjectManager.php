<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadataFactory;
interface ObjectManager
{
 public function find($className, $id);
 public function persist($object);
 public function remove($object);
 public function merge($object);
 public function clear($objectName = null);
 public function detach($object);
 public function refresh($object);
 public function flush();
 public function getRepository($className);
 public function getClassMetadata($className);
 public function getMetadataFactory();
 public function initializeObject($obj);
 public function contains($object);
}
