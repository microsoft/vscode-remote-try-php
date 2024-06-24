<?php
namespace MailPoetVendor\Symfony\Component\Finder\Iterator;
if (!defined('ABSPATH')) exit;
class DepthRangeFilterIterator extends \FilterIterator
{
 private $minDepth = 0;
 public function __construct(\RecursiveIteratorIterator $iterator, int $minDepth = 0, int $maxDepth = \PHP_INT_MAX)
 {
 $this->minDepth = $minDepth;
 $iterator->setMaxDepth(\PHP_INT_MAX === $maxDepth ? -1 : $maxDepth);
 parent::__construct($iterator);
 }
 #[\ReturnTypeWillChange]
 public function accept()
 {
 return $this->getInnerIterator()->getDepth() >= $this->minDepth;
 }
}
