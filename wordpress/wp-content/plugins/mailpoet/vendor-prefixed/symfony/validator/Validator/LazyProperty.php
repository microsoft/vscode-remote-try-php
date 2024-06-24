<?php
namespace MailPoetVendor\Symfony\Component\Validator\Validator;
if (!defined('ABSPATH')) exit;
class LazyProperty
{
 private $propertyValueCallback;
 public function __construct(\Closure $propertyValueCallback)
 {
 $this->propertyValueCallback = $propertyValueCallback;
 }
 public function getPropertyValue()
 {
 return ($this->propertyValueCallback)();
 }
}
