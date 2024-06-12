<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Valid extends Constraint
{
 public $traverse = \true;
 public function __get(string $option)
 {
 if ('groups' === $option) {
 // when this is reached, no groups have been configured
 return null;
 }
 return parent::__get($option);
 }
 public function addImplicitGroupName(string $group)
 {
 if (null !== $this->groups) {
 parent::addImplicitGroupName($group);
 }
 }
}
