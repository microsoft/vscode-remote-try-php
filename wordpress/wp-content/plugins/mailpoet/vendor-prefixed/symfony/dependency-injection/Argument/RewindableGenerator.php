<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
class RewindableGenerator implements \IteratorAggregate, \Countable
{
 private $generator;
 private $count;
 public function __construct(callable $generator, $count)
 {
 $this->generator = $generator;
 $this->count = $count;
 }
 public function getIterator() : \Traversable
 {
 $g = $this->generator;
 return $g();
 }
 public function count() : int
 {
 if (\is_callable($count = $this->count)) {
 $this->count = $count();
 }
 return $this->count;
 }
}
