<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use ArrayAccess;
use MailPoetVendor\Doctrine\ORM\AbstractQuery;
use Iterator;
use ReturnTypeWillChange;
use function key;
use function next;
use function reset;
class TreeWalkerChainIterator implements Iterator, ArrayAccess
{
 private $walkers = [];
 private $treeWalkerChain;
 private $query;
 private $parserResult;
 public function __construct(TreeWalkerChain $treeWalkerChain, $query, $parserResult)
 {
 $this->treeWalkerChain = $treeWalkerChain;
 $this->query = $query;
 $this->parserResult = $parserResult;
 }
 #[\ReturnTypeWillChange]
 public function rewind()
 {
 return reset($this->walkers);
 }
 #[\ReturnTypeWillChange]
 public function current()
 {
 return $this->offsetGet(key($this->walkers));
 }
 #[\ReturnTypeWillChange]
 public function key()
 {
 return key($this->walkers);
 }
 #[\ReturnTypeWillChange]
 public function next()
 {
 next($this->walkers);
 return $this->offsetGet(key($this->walkers));
 }
 #[\ReturnTypeWillChange]
 public function valid()
 {
 return key($this->walkers) !== null;
 }
 #[\ReturnTypeWillChange]
 public function offsetExists($offset)
 {
 return isset($this->walkers[$offset ?? '']);
 }
 #[\ReturnTypeWillChange]
 public function offsetGet($offset)
 {
 if ($this->offsetExists($offset)) {
 return new $this->walkers[$offset]($this->query, $this->parserResult, $this->treeWalkerChain->getQueryComponents());
 }
 return null;
 }
 #[\ReturnTypeWillChange]
 public function offsetSet($offset, $value)
 {
 if ($offset === null) {
 $this->walkers[] = $value;
 } else {
 $this->walkers[$offset] = $value;
 }
 }
 #[\ReturnTypeWillChange]
 public function offsetUnset($offset)
 {
 if ($this->offsetExists($offset)) {
 unset($this->walkers[$offset ?? '']);
 }
 }
}
