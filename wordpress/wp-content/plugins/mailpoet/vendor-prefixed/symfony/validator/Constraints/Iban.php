<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Iban extends Constraint
{
 public const INVALID_COUNTRY_CODE_ERROR = 'de78ee2c-bd50-44e2-aec8-3d8228aeadb9';
 public const INVALID_CHARACTERS_ERROR = '8d3d85e4-784f-4719-a5bc-d9e40d45a3a5';
 public const CHECKSUM_FAILED_ERROR = 'b9401321-f9bf-4dcb-83c1-f31094440795';
 public const INVALID_FORMAT_ERROR = 'c8d318f1-2ecc-41ba-b983-df70d225cf5a';
 public const NOT_SUPPORTED_COUNTRY_CODE_ERROR = 'e2c259f3-4b46-48e6-b72e-891658158ec8';
 protected static $errorNames = [self::INVALID_COUNTRY_CODE_ERROR => 'INVALID_COUNTRY_CODE_ERROR', self::INVALID_CHARACTERS_ERROR => 'INVALID_CHARACTERS_ERROR', self::CHECKSUM_FAILED_ERROR => 'CHECKSUM_FAILED_ERROR', self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR', self::NOT_SUPPORTED_COUNTRY_CODE_ERROR => 'NOT_SUPPORTED_COUNTRY_CODE_ERROR'];
 public $message = 'This is not a valid International Bank Account Number (IBAN).';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
