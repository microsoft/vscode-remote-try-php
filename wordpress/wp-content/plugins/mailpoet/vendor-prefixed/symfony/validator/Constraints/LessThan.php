<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class LessThan extends AbstractComparison
{
 public const TOO_HIGH_ERROR = '079d7420-2d13-460c-8756-de810eeb37d2';
 protected static $errorNames = [self::TOO_HIGH_ERROR => 'TOO_HIGH_ERROR'];
 public $message = 'This value should be less than {{ compared_value }}.';
}
