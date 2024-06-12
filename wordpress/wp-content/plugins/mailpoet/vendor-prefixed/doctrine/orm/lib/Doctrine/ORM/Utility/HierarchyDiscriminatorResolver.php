<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Utility;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadata;
final class HierarchyDiscriminatorResolver
{
 private function __construct()
 {
 }
 public static function resolveDiscriminatorsForClass(ClassMetadata $rootClassMetadata, EntityManagerInterface $entityManager) : array
 {
 $hierarchyClasses = $rootClassMetadata->subClasses;
 $hierarchyClasses[] = $rootClassMetadata->name;
 $discriminators = [];
 foreach ($hierarchyClasses as $class) {
 $currentMetadata = $entityManager->getClassMetadata($class);
 $currentDiscriminator = $currentMetadata->discriminatorValue;
 if ($currentDiscriminator !== null) {
 $discriminators[$currentDiscriminator] = null;
 }
 }
 return $discriminators;
 }
}
