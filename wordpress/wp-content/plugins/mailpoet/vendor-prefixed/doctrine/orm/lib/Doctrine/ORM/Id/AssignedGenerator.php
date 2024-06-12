<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Exception\EntityMissingAssignedId;
use function get_class;
class AssignedGenerator extends AbstractIdGenerator
{
 public function generateId(EntityManagerInterface $em, $entity)
 {
 $class = $em->getClassMetadata(get_class($entity));
 $idFields = $class->getIdentifierFieldNames();
 $identifier = [];
 foreach ($idFields as $idField) {
 $value = $class->getFieldValue($entity, $idField);
 if (!isset($value)) {
 throw EntityMissingAssignedId::forField($entity, $idField);
 }
 if (isset($class->associationMappings[$idField])) {
 // NOTE: Single Columns as associated identifiers only allowed - this constraint it is enforced.
 $value = $em->getUnitOfWork()->getSingleIdentifierValue($value);
 }
 $identifier[$idField] = $value;
 }
 return $identifier;
 }
}
