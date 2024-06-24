<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Isbn extends Constraint
{
 public const ISBN_10 = 'isbn10';
 public const ISBN_13 = 'isbn13';
 public const TOO_SHORT_ERROR = '949acbb0-8ef5-43ed-a0e9-032dfd08ae45';
 public const TOO_LONG_ERROR = '3171387d-f80a-47b3-bd6e-60598545316a';
 public const INVALID_CHARACTERS_ERROR = '23d21cea-da99-453d-98b1-a7d916fbb339';
 public const CHECKSUM_FAILED_ERROR = '2881c032-660f-46b6-8153-d352d9706640';
 public const TYPE_NOT_RECOGNIZED_ERROR = 'fa54a457-f042-441f-89c4-066ee5bdd3e1';
 protected static $errorNames = [self::TOO_SHORT_ERROR => 'TOO_SHORT_ERROR', self::TOO_LONG_ERROR => 'TOO_LONG_ERROR', self::INVALID_CHARACTERS_ERROR => 'INVALID_CHARACTERS_ERROR', self::CHECKSUM_FAILED_ERROR => 'CHECKSUM_FAILED_ERROR', self::TYPE_NOT_RECOGNIZED_ERROR => 'TYPE_NOT_RECOGNIZED_ERROR'];
 public $isbn10Message = 'This value is not a valid ISBN-10.';
 public $isbn13Message = 'This value is not a valid ISBN-13.';
 public $bothIsbnMessage = 'This value is neither a valid ISBN-10 nor a valid ISBN-13.';
 public $type;
 public $message;
 public function __construct($type = null, string $message = null, string $isbn10Message = null, string $isbn13Message = null, string $bothIsbnMessage = null, array $groups = null, $payload = null, array $options = [])
 {
 if (\is_array($type)) {
 $options = \array_merge($type, $options);
 } elseif (null !== $type) {
 $options['value'] = $type;
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->isbn10Message = $isbn10Message ?? $this->isbn10Message;
 $this->isbn13Message = $isbn13Message ?? $this->isbn13Message;
 $this->bothIsbnMessage = $bothIsbnMessage ?? $this->bothIsbnMessage;
 }
 public function getDefaultOption()
 {
 return 'type';
 }
}
