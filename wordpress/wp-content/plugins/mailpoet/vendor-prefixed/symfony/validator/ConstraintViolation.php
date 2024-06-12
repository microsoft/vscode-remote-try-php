<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
class ConstraintViolation implements ConstraintViolationInterface
{
 private $message;
 private $messageTemplate;
 private $parameters;
 private $plural;
 private $root;
 private $propertyPath;
 private $invalidValue;
 private $constraint;
 private $code;
 private $cause;
 public function __construct($message, ?string $messageTemplate, array $parameters, $root, ?string $propertyPath, $invalidValue, int $plural = null, string $code = null, Constraint $constraint = null, $cause = null)
 {
 if (!\is_string($message) && !(\is_object($message) && \method_exists($message, '__toString'))) {
 throw new \TypeError('Constraint violation message should be a string or an object which implements the __toString() method.');
 }
 $this->message = $message;
 $this->messageTemplate = $messageTemplate;
 $this->parameters = $parameters;
 $this->plural = $plural;
 $this->root = $root;
 $this->propertyPath = $propertyPath;
 $this->invalidValue = $invalidValue;
 $this->constraint = $constraint;
 $this->code = $code;
 $this->cause = $cause;
 }
 public function __toString()
 {
 if (\is_object($this->root)) {
 $class = 'Object(' . \get_class($this->root) . ')';
 } elseif (\is_array($this->root)) {
 $class = 'Array';
 } else {
 $class = (string) $this->root;
 }
 $propertyPath = (string) $this->propertyPath;
 if ('' !== $propertyPath && '[' !== $propertyPath[0] && '' !== $class) {
 $class .= '.';
 }
 if (null !== ($code = $this->code) && '' !== $code) {
 $code = ' (code ' . $code . ')';
 }
 return $class . $propertyPath . ":\n " . $this->getMessage() . $code;
 }
 public function getMessageTemplate()
 {
 return (string) $this->messageTemplate;
 }
 public function getParameters()
 {
 return $this->parameters;
 }
 public function getPlural()
 {
 return $this->plural;
 }
 public function getMessage()
 {
 return $this->message;
 }
 public function getRoot()
 {
 return $this->root;
 }
 public function getPropertyPath()
 {
 return (string) $this->propertyPath;
 }
 public function getInvalidValue()
 {
 return $this->invalidValue;
 }
 public function getConstraint()
 {
 return $this->constraint;
 }
 public function getCause()
 {
 return $this->cause;
 }
 public function getCode()
 {
 return $this->code;
 }
}
