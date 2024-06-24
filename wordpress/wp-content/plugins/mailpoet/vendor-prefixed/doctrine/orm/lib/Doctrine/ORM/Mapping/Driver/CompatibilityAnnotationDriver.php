<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\AnnotationDriver as PersistenceAnnotationDriver;
use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\MappingDriver;
use function class_exists;
if (!class_exists(PersistenceAnnotationDriver::class)) {
 abstract class CompatibilityAnnotationDriver implements MappingDriver
 {
 }
} else {
 abstract class CompatibilityAnnotationDriver extends PersistenceAnnotationDriver
 {
 }
}
