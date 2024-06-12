<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Type extends Constraint
{
 public const INVALID_TYPE_ERROR = 'ba785a8c-82cb-4283-967c-3cf342181b40';
 protected static $errorNames = [self::INVALID_TYPE_ERROR => 'INVALID_TYPE_ERROR'];
 public $message = 'This value should be of type {{ type }}.';
 public $type;
 public function __construct($type, string $message = null, array $groups = null, $payload = null, array $options = [])
 {
 if (\is_array($type) && \is_string(\key($type))) {
 $options = \array_merge($type, $options);
 } elseif (null !== $type) {
 $options['value'] = $type;
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
 public function getDefaultOption()
 {
 return 'type';
 }
 public function getRequiredOptions()
 {
 return ['type'];
 }
}
