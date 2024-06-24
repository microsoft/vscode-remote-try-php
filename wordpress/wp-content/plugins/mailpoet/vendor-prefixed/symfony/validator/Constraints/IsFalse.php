<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsFalse extends Constraint
{
 public const NOT_FALSE_ERROR = 'd53a91b0-def3-426a-83d7-269da7ab4200';
 protected static $errorNames = [self::NOT_FALSE_ERROR => 'NOT_FALSE_ERROR'];
 public $message = 'This value should be false.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
