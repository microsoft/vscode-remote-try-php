<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class TypeValidator extends ConstraintValidator
{
 private const VALIDATION_FUNCTIONS = ['bool' => 'is_bool', 'boolean' => 'is_bool', 'int' => 'is_int', 'integer' => 'is_int', 'long' => 'is_int', 'float' => 'is_float', 'double' => 'is_float', 'real' => 'is_float', 'numeric' => 'is_numeric', 'string' => 'is_string', 'scalar' => 'is_scalar', 'array' => 'is_array', 'iterable' => 'is_iterable', 'countable' => 'is_countable', 'callable' => 'is_callable', 'object' => 'is_object', 'resource' => 'is_resource', 'null' => 'is_null', 'alnum' => 'ctype_alnum', 'alpha' => 'ctype_alpha', 'cntrl' => 'ctype_cntrl', 'digit' => 'ctype_digit', 'graph' => 'ctype_graph', 'lower' => 'ctype_lower', 'print' => 'ctype_print', 'punct' => 'ctype_punct', 'space' => 'ctype_space', 'upper' => 'ctype_upper', 'xdigit' => 'ctype_xdigit'];
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Type) {
 throw new UnexpectedTypeException($constraint, Type::class);
 }
 if (null === $value) {
 return;
 }
 $types = (array) $constraint->type;
 foreach ($types as $type) {
 $type = \strtolower($type);
 if (isset(self::VALIDATION_FUNCTIONS[$type]) && self::VALIDATION_FUNCTIONS[$type]($value)) {
 return;
 }
 if ($value instanceof $type) {
 return;
 }
 }
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setParameter('{{ type }}', \implode('|', $types))->setCode(Type::INVALID_TYPE_ERROR)->addViolation();
 }
}
