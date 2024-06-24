<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Locales;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Locale extends Constraint
{
 public const NO_SUCH_LOCALE_ERROR = 'a0af4293-1f1a-4a1c-a328-979cba6182a2';
 protected static $errorNames = [self::NO_SUCH_LOCALE_ERROR => 'NO_SUCH_LOCALE_ERROR'];
 public $message = 'This value is not a valid locale.';
 public $canonicalize = \true;
 public function __construct(array $options = null, string $message = null, bool $canonicalize = null, array $groups = null, $payload = null)
 {
 if (!\class_exists(Locales::class)) {
 throw new LogicException('The Intl component is required to use the Locale constraint. Try running "composer require symfony/intl".');
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->canonicalize = $canonicalize ?? $this->canonicalize;
 }
}
