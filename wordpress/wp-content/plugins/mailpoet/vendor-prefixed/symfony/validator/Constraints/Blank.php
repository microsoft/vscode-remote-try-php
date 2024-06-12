<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Blank extends Constraint
{
 public const NOT_BLANK_ERROR = '183ad2de-533d-4796-a439-6d3c3852b549';
 protected static $errorNames = [self::NOT_BLANK_ERROR => 'NOT_BLANK_ERROR'];
 public $message = 'This value should be blank.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
