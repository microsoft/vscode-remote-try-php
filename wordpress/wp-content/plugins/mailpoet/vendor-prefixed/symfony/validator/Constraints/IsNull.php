<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsNull extends Constraint
{
 public const NOT_NULL_ERROR = '60d2f30b-8cfa-4372-b155-9656634de120';
 protected static $errorNames = [self::NOT_NULL_ERROR => 'NOT_NULL_ERROR'];
 public $message = 'This value should be null.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
