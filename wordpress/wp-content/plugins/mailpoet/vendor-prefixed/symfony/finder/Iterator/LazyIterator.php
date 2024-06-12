<?php
namespace MailPoetVendor\Symfony\Component\Finder\Iterator;
if (!defined('ABSPATH')) exit;
class LazyIterator implements \IteratorAggregate
{
 private $iteratorFactory;
 public function __construct(callable $iteratorFactory)
 {
 $this->iteratorFactory = $iteratorFactory;
 }
 public function getIterator() : \Traversable
 {
 yield from ($this->iteratorFactory)();
 }
}
