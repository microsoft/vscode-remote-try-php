<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Reference;
class ServiceLocatorArgument implements ArgumentInterface
{
 use ReferenceSetArgumentTrait;
 private $taggedIteratorArgument;
 public function __construct($values = [])
 {
 if ($values instanceof TaggedIteratorArgument) {
 $this->taggedIteratorArgument = $values;
 $this->values = [];
 } else {
 $this->setValues($values);
 }
 }
 public function getTaggedIteratorArgument() : ?TaggedIteratorArgument
 {
 return $this->taggedIteratorArgument;
 }
}
