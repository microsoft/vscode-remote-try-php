<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration;
if (!defined('ABSPATH')) exit;
use Iterator;
use ReturnTypeWillChange;
class IterableResult implements Iterator
{
 private $_hydrator;
 private $_rewinded = \false;
 private $_key = -1;
 private $_current = null;
 public function __construct($hydrator)
 {
 $this->_hydrator = $hydrator;
 }
 #[\ReturnTypeWillChange]
 public function rewind()
 {
 if ($this->_rewinded === \true) {
 throw new HydrationException('Can only iterate a Result once.');
 }
 $this->_current = $this->next();
 $this->_rewinded = \true;
 }
 #[\ReturnTypeWillChange]
 public function next()
 {
 $this->_current = $this->_hydrator->hydrateRow();
 $this->_key++;
 return $this->_current;
 }
 #[\ReturnTypeWillChange]
 public function current()
 {
 return $this->_current;
 }
 #[\ReturnTypeWillChange]
 public function key()
 {
 return $this->_key;
 }
 #[\ReturnTypeWillChange]
 public function valid()
 {
 return $this->_current !== \false;
 }
}
