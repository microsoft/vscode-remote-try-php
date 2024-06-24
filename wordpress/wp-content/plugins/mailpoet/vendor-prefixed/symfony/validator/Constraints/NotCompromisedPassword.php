<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotCompromisedPassword extends Constraint
{
 public const COMPROMISED_PASSWORD_ERROR = 'd9bcdbfe-a9d6-4bfa-a8ff-da5fd93e0f6d';
 protected static $errorNames = [self::COMPROMISED_PASSWORD_ERROR => 'COMPROMISED_PASSWORD_ERROR'];
 public $message = 'This password has been leaked in a data breach, it must not be used. Please use another password.';
 public $threshold = 1;
 public $skipOnError = \false;
 public function __construct(array $options = null, string $message = null, int $threshold = null, bool $skipOnError = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->threshold = $threshold ?? $this->threshold;
 $this->skipOnError = $skipOnError ?? $this->skipOnError;
 }
}
