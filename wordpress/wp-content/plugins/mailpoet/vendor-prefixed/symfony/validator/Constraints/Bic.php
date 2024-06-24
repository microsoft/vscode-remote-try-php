<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Countries;
use MailPoetVendor\Symfony\Component\PropertyAccess\PropertyAccess;
use MailPoetVendor\Symfony\Component\PropertyAccess\PropertyPathInterface;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Bic extends Constraint
{
 public const INVALID_LENGTH_ERROR = '66dad313-af0b-4214-8566-6c799be9789c';
 public const INVALID_CHARACTERS_ERROR = 'f424c529-7add-4417-8f2d-4b656e4833e2';
 public const INVALID_BANK_CODE_ERROR = '00559357-6170-4f29-aebd-d19330aa19cf';
 public const INVALID_COUNTRY_CODE_ERROR = '1ce76f8d-3c1f-451c-9e62-fe9c3ed486ae';
 public const INVALID_CASE_ERROR = '11884038-3312-4ae5-9d04-699f782130c7';
 public const INVALID_IBAN_COUNTRY_CODE_ERROR = '29a2c3bb-587b-4996-b6f5-53081364cea5';
 protected static $errorNames = [self::INVALID_LENGTH_ERROR => 'INVALID_LENGTH_ERROR', self::INVALID_CHARACTERS_ERROR => 'INVALID_CHARACTERS_ERROR', self::INVALID_BANK_CODE_ERROR => 'INVALID_BANK_CODE_ERROR', self::INVALID_COUNTRY_CODE_ERROR => 'INVALID_COUNTRY_CODE_ERROR', self::INVALID_CASE_ERROR => 'INVALID_CASE_ERROR'];
 public $message = 'This is not a valid Business Identifier Code (BIC).';
 public $ibanMessage = 'This Business Identifier Code (BIC) is not associated with IBAN {{ iban }}.';
 public $iban;
 public $ibanPropertyPath;
 public function __construct(array $options = null, string $message = null, string $iban = null, $ibanPropertyPath = null, string $ibanMessage = null, array $groups = null, $payload = null)
 {
 if (!\class_exists(Countries::class)) {
 throw new LogicException('The Intl component is required to use the Bic constraint. Try running "composer require symfony/intl".');
 }
 if (null !== $ibanPropertyPath && !\is_string($ibanPropertyPath) && !$ibanPropertyPath instanceof PropertyPathInterface) {
 throw new \TypeError(\sprintf('"%s": Expected argument $ibanPropertyPath to be either null, a string or an instance of "%s", got "%s".', __METHOD__, PropertyPathInterface::class, \get_debug_type($ibanPropertyPath)));
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->ibanMessage = $ibanMessage ?? $this->ibanMessage;
 $this->iban = $iban ?? $this->iban;
 $this->ibanPropertyPath = $ibanPropertyPath ?? $this->ibanPropertyPath;
 if (null !== $this->iban && null !== $this->ibanPropertyPath) {
 throw new ConstraintDefinitionException('The "iban" and "ibanPropertyPath" options of the Iban constraint cannot be used at the same time.');
 }
 if (null !== $this->ibanPropertyPath && !\class_exists(PropertyAccess::class)) {
 throw new LogicException(\sprintf('The "symfony/property-access" component is required to use the "%s" constraint with the "ibanPropertyPath" option.', self::class));
 }
 }
}
