<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
trigger_deprecation('symfony/validator', '5.2', '%s is deprecated.', NumberConstraintTrait::class);
trait NumberConstraintTrait
{
 private function configureNumberConstraintOptions($options) : array
 {
 if (null === $options) {
 $options = [];
 } elseif (!\is_array($options)) {
 $options = [$this->getDefaultOption() => $options];
 }
 if (isset($options['propertyPath'])) {
 throw new ConstraintDefinitionException(\sprintf('The "propertyPath" option of the "%s" constraint cannot be set.', static::class));
 }
 if (isset($options['value'])) {
 throw new ConstraintDefinitionException(\sprintf('The "value" option of the "%s" constraint cannot be set.', static::class));
 }
 $options['value'] = 0;
 return $options;
 }
}
