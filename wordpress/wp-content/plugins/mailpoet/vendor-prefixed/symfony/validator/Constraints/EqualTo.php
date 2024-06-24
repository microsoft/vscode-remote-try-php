<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class EqualTo extends AbstractComparison
{
 public const NOT_EQUAL_ERROR = '478618a7-95ba-473d-9101-cabd45e49115';
 protected static $errorNames = [self::NOT_EQUAL_ERROR => 'NOT_EQUAL_ERROR'];
 public $message = 'This value should be equal to {{ compared_value }}.';
}
