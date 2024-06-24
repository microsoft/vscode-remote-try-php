<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Luhn extends Constraint
{
 public const INVALID_CHARACTERS_ERROR = 'dfad6d23-1b74-4374-929b-5cbb56fc0d9e';
 public const CHECKSUM_FAILED_ERROR = '4d760774-3f50-4cd5-a6d5-b10a3299d8d3';
 protected static $errorNames = [self::INVALID_CHARACTERS_ERROR => 'INVALID_CHARACTERS_ERROR', self::CHECKSUM_FAILED_ERROR => 'CHECKSUM_FAILED_ERROR'];
 public $message = 'Invalid card number.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
