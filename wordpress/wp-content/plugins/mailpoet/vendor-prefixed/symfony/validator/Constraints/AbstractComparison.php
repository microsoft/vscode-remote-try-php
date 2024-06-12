<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\PropertyAccess\PropertyAccess;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
abstract class AbstractComparison extends Constraint
{
 public $message;
 public $value;
 public $propertyPath;
 public function __construct($value = null, $propertyPath = null, string $message = null, array $groups = null, $payload = null, array $options = [])
 {
 if (\is_array($value)) {
 $options = \array_merge($value, $options);
 } elseif (null !== $value) {
 $options['value'] = $value;
 }
 parent::__construct($options, $groups, $payload);
 $this->message = $message ?? $this->message;
 $this->propertyPath = $propertyPath ?? $this->propertyPath;
 if (null === $this->value && null === $this->propertyPath) {
 throw new ConstraintDefinitionException(\sprintf('The "%s" constraint requires either the "value" or "propertyPath" option to be set.', static::class));
 }
 if (null !== $this->value && null !== $this->propertyPath) {
 throw new ConstraintDefinitionException(\sprintf('The "%s" constraint requires only one of the "value" or "propertyPath" options to be set, not both.', static::class));
 }
 if (null !== $this->propertyPath && !\class_exists(PropertyAccess::class)) {
 throw new LogicException(\sprintf('The "%s" constraint requires the Symfony PropertyAccess component to use the "propertyPath" option.', static::class));
 }
 }
 public function getDefaultOption()
 {
 return 'value';
 }
}
