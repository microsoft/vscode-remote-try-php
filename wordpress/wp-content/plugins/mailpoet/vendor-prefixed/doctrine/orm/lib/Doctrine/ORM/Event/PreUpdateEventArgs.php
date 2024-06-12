<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use InvalidArgumentException;
use function get_debug_type;
use function sprintf;
class PreUpdateEventArgs extends LifecycleEventArgs
{
 private $entityChangeSet;
 public function __construct($entity, EntityManagerInterface $em, array &$changeSet)
 {
 parent::__construct($entity, $em);
 $this->entityChangeSet =& $changeSet;
 }
 public function getEntityChangeSet()
 {
 return $this->entityChangeSet;
 }
 public function hasChangedField($field)
 {
 return isset($this->entityChangeSet[$field]);
 }
 public function getOldValue($field)
 {
 $this->assertValidField($field);
 return $this->entityChangeSet[$field][0];
 }
 public function getNewValue($field)
 {
 $this->assertValidField($field);
 return $this->entityChangeSet[$field][1];
 }
 public function setNewValue($field, $value)
 {
 $this->assertValidField($field);
 $this->entityChangeSet[$field][1] = $value;
 }
 private function assertValidField(string $field) : void
 {
 if (!isset($this->entityChangeSet[$field])) {
 throw new InvalidArgumentException(sprintf('Field "%s" is not a valid field of the entity "%s" in PreUpdateEventArgs.', $field, get_debug_type($this->getEntity())));
 }
 }
}
