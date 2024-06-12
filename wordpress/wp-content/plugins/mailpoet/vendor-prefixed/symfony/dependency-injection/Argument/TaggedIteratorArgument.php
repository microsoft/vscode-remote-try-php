<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
class TaggedIteratorArgument extends IteratorArgument
{
 private $tag;
 private $indexAttribute;
 private $defaultIndexMethod;
 private $defaultPriorityMethod;
 private $needsIndexes = \false;
 public function __construct(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, bool $needsIndexes = \false, string $defaultPriorityMethod = null)
 {
 parent::__construct([]);
 if (null === $indexAttribute && $needsIndexes) {
 $indexAttribute = \preg_match('/[^.]++$/', $tag, $m) ? $m[0] : $tag;
 }
 $this->tag = $tag;
 $this->indexAttribute = $indexAttribute;
 $this->defaultIndexMethod = $defaultIndexMethod ?: ($indexAttribute ? 'getDefault' . \str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $indexAttribute))) . 'Name' : null);
 $this->needsIndexes = $needsIndexes;
 $this->defaultPriorityMethod = $defaultPriorityMethod ?: ($indexAttribute ? 'getDefault' . \str_replace(' ', '', \ucwords(\preg_replace('/[^a-zA-Z0-9\\x7f-\\xff]++/', ' ', $indexAttribute))) . 'Priority' : null);
 }
 public function getTag()
 {
 return $this->tag;
 }
 public function getIndexAttribute() : ?string
 {
 return $this->indexAttribute;
 }
 public function getDefaultIndexMethod() : ?string
 {
 return $this->defaultIndexMethod;
 }
 public function needsIndexes() : bool
 {
 return $this->needsIndexes;
 }
 public function getDefaultPriorityMethod() : ?string
 {
 return $this->defaultPriorityMethod;
 }
}
