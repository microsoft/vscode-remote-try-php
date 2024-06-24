<?php
namespace MailPoetVendor\Symfony\Component\Finder\Iterator;
if (!defined('ABSPATH')) exit;
class FilecontentFilterIterator extends MultiplePcreFilterIterator
{
 #[\ReturnTypeWillChange]
 public function accept()
 {
 if (!$this->matchRegexps && !$this->noMatchRegexps) {
 return \true;
 }
 $fileinfo = $this->current();
 if ($fileinfo->isDir() || !$fileinfo->isReadable()) {
 return \false;
 }
 $content = $fileinfo->getContents();
 if (!$content) {
 return \false;
 }
 return $this->isAccepted($content);
 }
 protected function toRegex($str)
 {
 return $this->isRegex($str) ? $str : '/' . \preg_quote($str, '/') . '/';
 }
}
