<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\InvalidArgumentException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Url extends Constraint
{
 public const INVALID_URL_ERROR = '57c2f299-1154-4870-89bb-ef3b1f5ad229';
 protected static $errorNames = [self::INVALID_URL_ERROR => 'INVALID_URL_ERROR'];
 public $message = 'This value is not a valid URL.';
 public $protocols = ['http', 'https'];
 public $relativeProtocol = \false;
 public $normalizer;
 public function __construct(array $options = null, string $message = null, array $protocols = null, bool $relativeProtocol = null, callable $normalizer = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->protocols = $protocols ?? $this->protocols;
 $this->relativeProtocol = $relativeProtocol ?? $this->relativeProtocol;
 $this->normalizer = $normalizer ?? $this->normalizer;
 if (null !== $this->normalizer && !\is_callable($this->normalizer)) {
 throw new InvalidArgumentException(\sprintf('The "normalizer" option must be a valid callable ("%s" given).', \get_debug_type($this->normalizer)));
 }
 }
}
