<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Languages;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Language extends Constraint
{
 public const NO_SUCH_LANGUAGE_ERROR = 'ee65fec4-9a20-4202-9f39-ca558cd7bdf7';
 protected static $errorNames = [self::NO_SUCH_LANGUAGE_ERROR => 'NO_SUCH_LANGUAGE_ERROR'];
 public $message = 'This value is not a valid language.';
 public $alpha3 = \false;
 public function __construct(array $options = null, string $message = null, bool $alpha3 = null, array $groups = null, $payload = null)
 {
 if (!\class_exists(Languages::class)) {
 throw new LogicException('The Intl component is required to use the Language constraint. Try running "composer require symfony/intl".');
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->alpha3 = $alpha3 ?? $this->alpha3;
 }
}
