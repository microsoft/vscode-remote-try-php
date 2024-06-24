<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Date extends Constraint
{
 public const INVALID_FORMAT_ERROR = '69819696-02ac-4a99-9ff0-14e127c4d1bc';
 public const INVALID_DATE_ERROR = '3c184ce5-b31d-4de7-8b76-326da7b2be93';
 protected static $errorNames = [self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR', self::INVALID_DATE_ERROR => 'INVALID_DATE_ERROR'];
 public $message = 'This value is not a valid date.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
