<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotIdenticalTo extends AbstractComparison
{
 public const IS_IDENTICAL_ERROR = '4aaac518-0dda-4129-a6d9-e216b9b454a0';
 protected static $errorNames = [self::IS_IDENTICAL_ERROR => 'IS_IDENTICAL_ERROR'];
 public $message = 'This value should not be identical to {{ compared_value_type }} {{ compared_value }}.';
}
