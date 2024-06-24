<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidArgumentException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Unique extends Constraint
{
 public const IS_NOT_UNIQUE = '7911c98d-b845-4da0-94b7-a8dac36bc55a';
 protected static $errorNames = [self::IS_NOT_UNIQUE => 'IS_NOT_UNIQUE'];
 public $message = 'This collection should contain only unique elements.';
 public $normalizer;
 public function __construct(array $options = null, string $message = null, callable $normalizer = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->normalizer = $normalizer ?? $this->normalizer;
 if (null !== $this->normalizer && !\is_callable($this->normalizer)) {
 throw new InvalidArgumentException(\sprintf('The "normalizer" option must be a valid callable ("%s" given).', \get_debug_type($this->normalizer)));
 }
 }
}
