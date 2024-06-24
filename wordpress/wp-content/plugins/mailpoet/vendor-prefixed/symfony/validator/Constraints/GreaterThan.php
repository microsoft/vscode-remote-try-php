<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class GreaterThan extends AbstractComparison
{
 public const TOO_LOW_ERROR = '778b7ae0-84d3-481a-9dec-35fdb64b1d78';
 protected static $errorNames = [self::TOO_LOW_ERROR => 'TOO_LOW_ERROR'];
 public $message = 'This value should be greater than {{ compared_value }}.';
}
