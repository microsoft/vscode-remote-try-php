<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\AbstractLazyCollection;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\Common\Collections\Selectable;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
use ReturnTypeWillChange;
class LazyCriteriaCollection extends AbstractLazyCollection implements Selectable
{
 protected $entityPersister;
 protected $criteria;
 private $count;
 public function __construct(EntityPersister $entityPersister, Criteria $criteria)
 {
 $this->entityPersister = $entityPersister;
 $this->criteria = $criteria;
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 if ($this->isInitialized()) {
 return $this->collection->count();
 }
 // Return cached result in case count query was already executed
 if ($this->count !== null) {
 return $this->count;
 }
 return $this->count = $this->entityPersister->count($this->criteria);
 }
 public function isEmpty()
 {
 if ($this->isInitialized()) {
 return $this->collection->isEmpty();
 }
 return !$this->count();
 }
 public function contains($element)
 {
 if ($this->isInitialized()) {
 return $this->collection->contains($element);
 }
 return $this->entityPersister->exists($element, $this->criteria);
 }
 public function matching(Criteria $criteria)
 {
 $this->initialize();
 return $this->collection->matching($criteria);
 }
 protected function doInitialize()
 {
 $elements = $this->entityPersister->loadCriteria($this->criteria);
 $this->collection = new ArrayCollection($elements);
 }
}
