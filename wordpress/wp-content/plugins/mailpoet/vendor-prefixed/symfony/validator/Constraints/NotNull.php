<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotNull extends Constraint
{
 public const IS_NULL_ERROR = 'ad32d13f-c3d4-423b-909a-857b961eb720';
 protected static $errorNames = [self::IS_NULL_ERROR => 'IS_NULL_ERROR'];
 public $message = 'This value should not be null.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
