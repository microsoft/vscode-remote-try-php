<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotEqualTo extends AbstractComparison
{
 public const IS_EQUAL_ERROR = 'aa2e33da-25c8-4d76-8c6c-812f02ea89dd';
 protected static $errorNames = [self::IS_EQUAL_ERROR => 'IS_EQUAL_ERROR'];
 public $message = 'This value should not be equal to {{ compared_value }}.';
}
