<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
abstract class Composite extends Constraint
{
 public function __construct($options = null, array $groups = null, $payload = null)
 {
 parent::__construct($options, $groups, $payload);
 $this->initializeNestedConstraints();
 $compositeOption = $this->getCompositeOption();
 $nestedConstraints = $this->{$compositeOption};
 if (!\is_array($nestedConstraints)) {
 $nestedConstraints = [$nestedConstraints];
 }
 foreach ($nestedConstraints as $constraint) {
 if (!$constraint instanceof Constraint) {
 if (\is_object($constraint)) {
 $constraint = \get_class($constraint);
 }
 throw new ConstraintDefinitionException(\sprintf('The value "%s" is not an instance of Constraint in constraint "%s".', $constraint, static::class));
 }
 if ($constraint instanceof Valid) {
 throw new ConstraintDefinitionException(\sprintf('The constraint Valid cannot be nested inside constraint "%s". You can only declare the Valid constraint directly on a field or method.', static::class));
 }
 }
 if (!isset(((array) $this)['groups'])) {
 $mergedGroups = [];
 foreach ($nestedConstraints as $constraint) {
 foreach ($constraint->groups as $group) {
 $mergedGroups[$group] = \true;
 }
 }
 // prevent empty composite constraint to have empty groups
 $this->groups = \array_keys($mergedGroups) ?: [self::DEFAULT_GROUP];
 $this->{$compositeOption} = $nestedConstraints;
 return;
 }
 foreach ($nestedConstraints as $constraint) {
 if (isset(((array) $constraint)['groups'])) {
 $excessGroups = \array_diff($constraint->groups, $this->groups);
 if (\count($excessGroups) > 0) {
 throw new ConstraintDefinitionException(\sprintf('The group(s) "%s" passed to the constraint "%s" should also be passed to its containing constraint "%s".', \implode('", "', $excessGroups), \get_debug_type($constraint), static::class));
 }
 } else {
 $constraint->groups = $this->groups;
 }
 }
 $this->{$compositeOption} = $nestedConstraints;
 }
 public function addImplicitGroupName(string $group)
 {
 parent::addImplicitGroupName($group);
 $nestedConstraints = $this->{$this->getCompositeOption()};
 foreach ($nestedConstraints as $constraint) {
 $constraint->addImplicitGroupName($group);
 }
 }
 protected abstract function getCompositeOption();
 public function getNestedConstraints()
 {
 return $this->{$this->getCompositeOption()};
 }
 protected function initializeNestedConstraints()
 {
 }
}
