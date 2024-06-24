<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Time extends Constraint
{
 public const INVALID_FORMAT_ERROR = '9d27b2bb-f755-4fbf-b725-39b1edbdebdf';
 public const INVALID_TIME_ERROR = '8532f9e1-84b2-4d67-8989-0818bc38533b';
 protected static $errorNames = [self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR', self::INVALID_TIME_ERROR => 'INVALID_TIME_ERROR'];
 public $message = 'This value is not a valid time.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
