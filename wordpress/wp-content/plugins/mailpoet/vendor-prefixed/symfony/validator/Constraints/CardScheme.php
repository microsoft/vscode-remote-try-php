<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CardScheme extends Constraint
{
 public const AMEX = 'AMEX';
 public const CHINA_UNIONPAY = 'CHINA_UNIONPAY';
 public const DINERS = 'DINERS';
 public const DISCOVER = 'DISCOVER';
 public const INSTAPAYMENT = 'INSTAPAYMENT';
 public const JCB = 'JCB';
 public const LASER = 'LASER';
 public const MAESTRO = 'MAESTRO';
 public const MASTERCARD = 'MASTERCARD';
 public const MIR = 'MIR';
 public const UATP = 'UATP';
 public const VISA = 'VISA';
 public const NOT_NUMERIC_ERROR = 'a2ad9231-e827-485f-8a1e-ef4d9a6d5c2e';
 public const INVALID_FORMAT_ERROR = 'a8faedbf-1c2f-4695-8d22-55783be8efed';
 protected static $errorNames = [self::NOT_NUMERIC_ERROR => 'NOT_NUMERIC_ERROR', self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR'];
 public $message = 'Unsupported card type or invalid card number.';
 public $schemes;
 public function __construct($schemes, string $message = null, array $groups = null, $payload = null, array $options = [])
 {
 if (\is_array($schemes) && \is_string(\key($schemes))) {
 $options = \array_merge($schemes, $options);
 } else {
 $options['value'] = $schemes;
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
 public function getDefaultOption()
 {
 return 'schemes';
 }
 public function getRequiredOptions()
 {
 return ['schemes'];
 }
}
