<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use ReflectionClass;
interface ClassMetadata
{
 public function getName();
 public function getIdentifier();
 public function getReflectionClass();
 public function isIdentifier($fieldName);
 public function hasField($fieldName);
 public function hasAssociation($fieldName);
 public function isSingleValuedAssociation($fieldName);
 public function isCollectionValuedAssociation($fieldName);
 public function getFieldNames();
 public function getIdentifierFieldNames();
 public function getAssociationNames();
 public function getTypeOfField($fieldName);
 public function getAssociationTargetClass($assocName);
 public function isAssociationInverseSide($assocName);
 public function getAssociationMappedByTargetField($assocName);
 public function getIdentifierValues($object);
}
