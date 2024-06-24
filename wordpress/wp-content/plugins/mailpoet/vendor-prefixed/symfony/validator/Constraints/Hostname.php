<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Hostname extends Constraint
{
 public const INVALID_HOSTNAME_ERROR = '7057ffdb-0af4-4f7e-bd5e-e9acfa6d7a2d';
 protected static $errorNames = [self::INVALID_HOSTNAME_ERROR => 'INVALID_HOSTNAME_ERROR'];
 public $message = 'This value is not a valid hostname.';
 public $requireTld = \true;
 public function __construct(array $options = null, string $message = null, bool $requireTld = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->requireTld = $requireTld ?? $this->requireTld;
 }
}
