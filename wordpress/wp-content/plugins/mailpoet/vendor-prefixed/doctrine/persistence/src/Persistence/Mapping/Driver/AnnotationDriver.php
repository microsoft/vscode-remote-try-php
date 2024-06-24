<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use function get_class;
abstract class AnnotationDriver implements MappingDriver
{
 use ColocatedMappingDriver;
 protected $reader;
 protected $entityAnnotationClasses = [];
 public function __construct($reader, $paths = null)
 {
 $this->reader = $reader;
 $this->addPaths((array) $paths);
 }
 public function getReader()
 {
 return $this->reader;
 }
 public function isTransient($className)
 {
 $classAnnotations = $this->reader->getClassAnnotations(new ReflectionClass($className));
 foreach ($classAnnotations as $annot) {
 if (isset($this->entityAnnotationClasses[get_class($annot)])) {
 return \false;
 }
 }
 return \true;
 }
}
