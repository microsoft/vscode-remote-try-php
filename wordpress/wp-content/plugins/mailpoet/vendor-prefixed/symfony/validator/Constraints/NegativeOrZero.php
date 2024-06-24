<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NegativeOrZero extends LessThanOrEqual
{
 use ZeroComparisonConstraintTrait;
 public $message = 'This value should be either negative or zero.';
}
