<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Countries;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Country extends Constraint
{
 public const NO_SUCH_COUNTRY_ERROR = '8f900c12-61bd-455d-9398-996cd040f7f0';
 protected static $errorNames = [self::NO_SUCH_COUNTRY_ERROR => 'NO_SUCH_COUNTRY_ERROR'];
 public $message = 'This value is not a valid country.';
 public $alpha3 = \false;
 public function __construct(array $options = null, string $message = null, bool $alpha3 = null, array $groups = null, $payload = null)
 {
 if (!\class_exists(Countries::class)) {
 throw new LogicException('The Intl component is required to use the Country constraint. Try running "composer require symfony/intl".');
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->alpha3 = $alpha3 ?? $this->alpha3;
 }
}
