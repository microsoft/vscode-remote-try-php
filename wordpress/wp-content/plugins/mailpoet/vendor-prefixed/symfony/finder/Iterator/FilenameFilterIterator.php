<?php
namespace MailPoetVendor\Symfony\Component\Finder\Iterator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Finder\Glob;
class FilenameFilterIterator extends MultiplePcreFilterIterator
{
 #[\ReturnTypeWillChange]
 public function accept()
 {
 return $this->isAccepted($this->current()->getFilename());
 }
 protected function toRegex($str)
 {
 return $this->isRegex($str) ? $str : Glob::toRegex($str);
 }
}
