<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Currencies;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Currency extends Constraint
{
 public const NO_SUCH_CURRENCY_ERROR = '69945ac1-2db4-405f-bec7-d2772f73df52';
 protected static $errorNames = [self::NO_SUCH_CURRENCY_ERROR => 'NO_SUCH_CURRENCY_ERROR'];
 public $message = 'This value is not a valid currency.';
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 if (!\class_exists(Currencies::class)) {
 throw new LogicException('The Intl component is required to use the Currency constraint. Try running "composer require symfony/intl".');
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 }
}
