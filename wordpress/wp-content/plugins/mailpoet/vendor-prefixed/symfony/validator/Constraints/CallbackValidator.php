<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class CallbackValidator extends ConstraintValidator
{
 public function validate($object, Constraint $constraint)
 {
 if (!$constraint instanceof Callback) {
 throw new UnexpectedTypeException($constraint, Callback::class);
 }
 $method = $constraint->callback;
 if ($method instanceof \Closure) {
 $method($object, $this->context, $constraint->payload);
 } elseif (\is_array($method)) {
 if (!\is_callable($method)) {
 if (isset($method[0]) && \is_object($method[0])) {
 $method[0] = \get_class($method[0]);
 }
 throw new ConstraintDefinitionException(\json_encode($method) . ' targeted by Callback constraint is not a valid callable.');
 }
 $method($object, $this->context, $constraint->payload);
 } elseif (null !== $object) {
 if (!\method_exists($object, $method)) {
 throw new ConstraintDefinitionException(\sprintf('Method "%s" targeted by Callback constraint does not exist in class "%s".', $method, \get_debug_type($object)));
 }
 $reflMethod = new \ReflectionMethod($object, $method);
 if ($reflMethod->isStatic()) {
 $reflMethod->invoke(null, $object, $this->context, $constraint->payload);
 } else {
 $reflMethod->invoke($object, $this->context, $constraint->payload);
 }
 }
 }
}
