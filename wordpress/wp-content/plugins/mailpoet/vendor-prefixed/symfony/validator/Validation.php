<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidationFailedException;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
final class Validation
{
 public static function createCallable($constraintOrValidator = null, Constraint ...$constraints) : callable
 {
 $validator = self::createIsValidCallable($constraintOrValidator, ...$constraints);
 return static function ($value) use($validator) {
 if (!$validator($value, $violations)) {
 throw new ValidationFailedException($value, $violations);
 }
 return $value;
 };
 }
 public static function createIsValidCallable($constraintOrValidator = null, Constraint ...$constraints) : callable
 {
 $validator = $constraintOrValidator;
 if ($constraintOrValidator instanceof Constraint) {
 $constraints = \func_get_args();
 $validator = null;
 } elseif (null !== $constraintOrValidator && !$constraintOrValidator instanceof ValidatorInterface) {
 throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be a "%s" or a "%s" object, "%s" given.', __METHOD__, Constraint::class, ValidatorInterface::class, \get_debug_type($constraintOrValidator)));
 }
 $validator = $validator ?? self::createValidator();
 return static function ($value, &$violations = null) use($constraints, $validator) {
 $violations = $validator->validate($value, $constraints);
 return 0 === $violations->count();
 };
 }
 public static function createValidator() : ValidatorInterface
 {
 return self::createValidatorBuilder()->getValidator();
 }
 public static function createValidatorBuilder() : ValidatorBuilder
 {
 return new ValidatorBuilder();
 }
 private function __construct()
 {
 }
}
