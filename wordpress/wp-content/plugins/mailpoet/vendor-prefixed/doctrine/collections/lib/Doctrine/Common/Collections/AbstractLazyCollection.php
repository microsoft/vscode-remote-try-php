<?php
namespace MailPoetVendor\Doctrine\Common\Collections;
if (!defined('ABSPATH')) exit;
use Closure;
use ReturnTypeWillChange;
use Traversable;
abstract class AbstractLazyCollection implements Collection
{
 protected $collection;
 protected $initialized = \false;
 #[\ReturnTypeWillChange]
 public function count()
 {
 $this->initialize();
 return $this->collection->count();
 }
 public function add($element)
 {
 $this->initialize();
 return $this->collection->add($element);
 }
 public function clear()
 {
 $this->initialize();
 $this->collection->clear();
 }
 public function contains($element)
 {
 $this->initialize();
 return $this->collection->contains($element);
 }
 public function isEmpty()
 {
 $this->initialize();
 return $this->collection->isEmpty();
 }
 public function remove($key)
 {
 $this->initialize();
 return $this->collection->remove($key);
 }
 public function removeElement($element)
 {
 $this->initialize();
 return $this->collection->removeElement($element);
 }
 public function containsKey($key)
 {
 $this->initialize();
 return $this->collection->containsKey($key);
 }
 public function get($key)
 {
 $this->initialize();
 return $this->collection->get($key);
 }
 public function getKeys()
 {
 $this->initialize();
 return $this->collection->getKeys();
 }
 public function getValues()
 {
 $this->initialize();
 return $this->collection->getValues();
 }
 public function set($key, $value)
 {
 $this->initialize();
 $this->collection->set($key, $value);
 }
 public function toArray()
 {
 $this->initialize();
 return $this->collection->toArray();
 }
 public function first()
 {
 $this->initialize();
 return $this->collection->first();
 }
 public function last()
 {
 $this->initialize();
 return $this->collection->last();
 }
 public function key()
 {
 $this->initialize();
 return $this->collection->key();
 }
 public function current()
 {
 $this->initialize();
 return $this->collection->current();
 }
 public function next()
 {
 $this->initialize();
 return $this->collection->next();
 }
 public function exists(Closure $p)
 {
 $this->initialize();
 return $this->collection->exists($p);
 }
 public function filter(Closure $p)
 {
 $this->initialize();
 return $this->collection->filter($p);
 }
 public function forAll(Closure $p)
 {
 $this->initialize();
 return $this->collection->forAll($p);
 }
 public function map(Closure $func)
 {
 $this->initialize();
 return $this->collection->map($func);
 }
 public function partition(Closure $p)
 {
 $this->initialize();
 return $this->collection->partition($p);
 }
 public function indexOf($element)
 {
 $this->initialize();
 return $this->collection->indexOf($element);
 }
 public function slice($offset, $length = null)
 {
 $this->initialize();
 return $this->collection->slice($offset, $length);
 }
 #[\ReturnTypeWillChange]
 public function getIterator()
 {
 $this->initialize();
 return $this->collection->getIterator();
 }
 #[\ReturnTypeWillChange]
 public function offsetExists($offset)
 {
 $this->initialize();
 return $this->collection->offsetExists($offset);
 }
 #[\ReturnTypeWillChange]
 public function offsetGet($offset)
 {
 $this->initialize();
 return $this->collection->offsetGet($offset);
 }
 #[\ReturnTypeWillChange]
 public function offsetSet($offset, $value)
 {
 $this->initialize();
 $this->collection->offsetSet($offset, $value);
 }
 #[\ReturnTypeWillChange]
 public function offsetUnset($offset)
 {
 $this->initialize();
 $this->collection->offsetUnset($offset);
 }
 public function isInitialized()
 {
 return $this->initialized;
 }
 protected function initialize()
 {
 if ($this->initialized) {
 return;
 }
 $this->doInitialize();
 $this->initialized = \true;
 }
 protected abstract function doInitialize();
}
