<?php
namespace MailPoetVendor\Symfony\Component\Finder\Iterator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Finder\Comparator\NumberComparator;
class SizeRangeFilterIterator extends \FilterIterator
{
 private $comparators = [];
 public function __construct(\Iterator $iterator, array $comparators)
 {
 $this->comparators = $comparators;
 parent::__construct($iterator);
 }
 #[\ReturnTypeWillChange]
 public function accept()
 {
 $fileinfo = $this->current();
 if (!$fileinfo->isFile()) {
 return \true;
 }
 $filesize = $fileinfo->getSize();
 foreach ($this->comparators as $compare) {
 if (!$compare->test($filesize)) {
 return \false;
 }
 }
 return \true;
 }
}
