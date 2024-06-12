<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Callback extends Constraint
{
 public $callback;
 public function __construct($callback = null, array $groups = null, $payload = null, array $options = [])
 {
 // Invocation through annotations with an array parameter only
 if (\is_array($callback) && 1 === \count($callback) && isset($callback['value'])) {
 $callback = $callback['value'];
 }
 if (!\is_array($callback) || !isset($callback['callback']) && !isset($callback['groups']) && !isset($callback['payload'])) {
 $options['callback'] = $callback;
 } else {
 $options = \array_merge($callback, $options);
 }
 parent::__construct($options, $groups, $payload);
 }
 public function getDefaultOption()
 {
 return 'callback';
 }
 public function getTargets()
 {
 return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
 }
}
