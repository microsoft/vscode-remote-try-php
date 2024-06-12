<?php
namespace MailPoetVendor\Symfony\Component\Validator\Exception;
if (!defined('ABSPATH')) exit;
class UnexpectedTypeException extends ValidatorException
{
 public function __construct($value, string $expectedType)
 {
 parent::__construct(\sprintf('Expected argument of type "%s", "%s" given', $expectedType, \get_debug_type($value)));
 }
}
