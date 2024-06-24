<?php
namespace MailPoetVendor\Symfony\Component\Finder\Iterator;
if (!defined('ABSPATH')) exit;
class PathFilterIterator extends MultiplePcreFilterIterator
{
 #[\ReturnTypeWillChange]
 public function accept()
 {
 $filename = $this->current()->getRelativePathname();
 if ('\\' === \DIRECTORY_SEPARATOR) {
 $filename = \str_replace('\\', '/', $filename);
 }
 return $this->isAccepted($filename);
 }
 protected function toRegex($str)
 {
 return $this->isRegex($str) ? $str : '/' . \preg_quote($str, '/') . '/';
 }
}
