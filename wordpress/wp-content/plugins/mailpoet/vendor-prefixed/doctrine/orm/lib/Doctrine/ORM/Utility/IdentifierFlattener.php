<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Utility;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use MailPoetVendor\Doctrine\Persistence\Mapping\ClassMetadataFactory;
use function assert;
use function implode;
use function is_a;
final class IdentifierFlattener
{
 private $unitOfWork;
 private $metadataFactory;
 public function __construct(UnitOfWork $unitOfWork, ClassMetadataFactory $metadataFactory)
 {
 $this->unitOfWork = $unitOfWork;
 $this->metadataFactory = $metadataFactory;
 }
 public function flattenIdentifier(ClassMetadata $class, array $id) : array
 {
 $flatId = [];
 foreach ($class->identifier as $field) {
 if (isset($class->associationMappings[$field]) && isset($id[$field]) && is_a($id[$field], $class->associationMappings[$field]['targetEntity'])) {
 $targetClassMetadata = $this->metadataFactory->getMetadataFor($class->associationMappings[$field]['targetEntity']);
 assert($targetClassMetadata instanceof ClassMetadata);
 if ($this->unitOfWork->isInIdentityMap($id[$field])) {
 $associatedId = $this->flattenIdentifier($targetClassMetadata, $this->unitOfWork->getEntityIdentifier($id[$field]));
 } else {
 $associatedId = $this->flattenIdentifier($targetClassMetadata, $targetClassMetadata->getIdentifierValues($id[$field]));
 }
 $flatId[$field] = implode(' ', $associatedId);
 } elseif (isset($class->associationMappings[$field])) {
 $associatedId = [];
 foreach ($class->associationMappings[$field]['joinColumns'] as $joinColumn) {
 $associatedId[] = $id[$joinColumn['name']];
 }
 $flatId[$field] = implode(' ', $associatedId);
 } else {
 if ($id[$field] instanceof BackedEnum) {
 $flatId[$field] = $id[$field]->value;
 } else {
 $flatId[$field] = $id[$field];
 }
 }
 }
 return $flatId;
 }
}
