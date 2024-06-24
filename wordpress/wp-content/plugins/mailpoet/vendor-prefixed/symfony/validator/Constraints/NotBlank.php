<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidArgumentException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlank extends Constraint
{
 public const IS_BLANK_ERROR = 'c1051bb4-d103-4f74-8988-acbcafc7fdc3';
 protected static $errorNames = [self::IS_BLANK_ERROR => 'IS_BLANK_ERROR'];
 public $message = 'This value should not be blank.';
 public $allowNull = \false;
 public $normalizer;
 public function __construct(array $options = null, string $message = null, bool $allowNull = null, callable $normalizer = null, array $groups = null, $payload = null)
 {
 parent::__construct($options ?? [], $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->allowNull = $allowNull ?? $this->allowNull;
 $this->normalizer = $normalizer ?? $this->normalizer;
 if (null !== $this->normalizer && !\is_callable($this->normalizer)) {
 throw new InvalidArgumentException(\sprintf('The "normalizer" option must be a valid callable ("%s" given).', \get_debug_type($this->normalizer)));
 }
 }
}
