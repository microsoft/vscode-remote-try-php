<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\Keywords;
if (!defined('ABSPATH')) exit;
use function array_flip;
use function array_map;
use function strtoupper;
abstract class KeywordList
{
 private $keywords;
 public function isKeyword($word)
 {
 if ($this->keywords === null) {
 $this->initializeKeywords();
 }
 return isset($this->keywords[strtoupper($word)]);
 }
 protected function initializeKeywords()
 {
 $this->keywords = array_flip(array_map('strtoupper', $this->getKeywords()));
 }
 protected abstract function getKeywords();
 public abstract function getName();
}
