<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsTrue extends Constraint
{
 public const NOT_TRUE_ERROR = '2beabf1c-54c0-4882-a928-05249b26e23b';
 protected static $errorNames = [self::NOT_TRUE_ERROR => 'NOT_TRUE_ERROR'];
 public $message = 'This value should be true.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
