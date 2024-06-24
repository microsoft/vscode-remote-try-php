<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class GreaterThanOrEqual extends AbstractComparison
{
 public const TOO_LOW_ERROR = 'ea4e51d1-3342-48bd-87f1-9e672cd90cad';
 protected static $errorNames = [self::TOO_LOW_ERROR => 'TOO_LOW_ERROR'];
 public $message = 'This value should be greater than or equal to {{ compared_value }}.';
}
