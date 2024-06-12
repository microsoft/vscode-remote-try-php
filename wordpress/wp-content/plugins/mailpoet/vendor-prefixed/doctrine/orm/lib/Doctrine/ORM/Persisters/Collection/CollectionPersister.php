<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Collection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
interface CollectionPersister
{
 public function delete(PersistentCollection $collection);
 public function update(PersistentCollection $collection);
 public function count(PersistentCollection $collection);
 public function slice(PersistentCollection $collection, $offset, $length = null);
 public function contains(PersistentCollection $collection, $element);
 public function containsKey(PersistentCollection $collection, $key);
 public function get(PersistentCollection $collection, $index);
 public function loadCriteria(PersistentCollection $collection, Criteria $criteria);
}
