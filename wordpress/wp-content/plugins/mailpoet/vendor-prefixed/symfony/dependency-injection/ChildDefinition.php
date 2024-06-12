<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
class ChildDefinition extends Definition
{
 private $parent;
 public function __construct(string $parent)
 {
 $this->parent = $parent;
 }
 public function getParent()
 {
 return $this->parent;
 }
 public function setParent(string $parent)
 {
 $this->parent = $parent;
 return $this;
 }
 public function getArgument($index)
 {
 if (\array_key_exists('index_' . $index, $this->arguments)) {
 return $this->arguments['index_' . $index];
 }
 return parent::getArgument($index);
 }
 public function replaceArgument($index, $value)
 {
 if (\is_int($index)) {
 $this->arguments['index_' . $index] = $value;
 } elseif (\str_starts_with($index, '$')) {
 $this->arguments[$index] = $value;
 } else {
 throw new InvalidArgumentException('The argument must be an existing index or the name of a constructor\'s parameter.');
 }
 return $this;
 }
}
