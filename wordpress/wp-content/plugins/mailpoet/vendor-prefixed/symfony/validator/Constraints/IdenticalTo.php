<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IdenticalTo extends AbstractComparison
{
 public const NOT_IDENTICAL_ERROR = '2a8cc50f-58a2-4536-875e-060a2ce69ed5';
 protected static $errorNames = [self::NOT_IDENTICAL_ERROR => 'NOT_IDENTICAL_ERROR'];
 public $message = 'This value should be identical to {{ compared_value_type }} {{ compared_value }}.';
}
