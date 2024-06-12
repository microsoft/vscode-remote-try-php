<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
trait ZeroComparisonConstraintTrait
{
 public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null)
 {
 if (null === $options) {
 $options = [];
 }
 if (isset($options['propertyPath'])) {
 throw new ConstraintDefinitionException(\sprintf('The "propertyPath" option of the "%s" constraint cannot be set.', static::class));
 }
 if (isset($options['value'])) {
 throw new ConstraintDefinitionException(\sprintf('The "value" option of the "%s" constraint cannot be set.', static::class));
 }
 parent::__construct(0, null, $message, $groups, $payload, $options);
 }
 public function validatedBy() : string
 {
 return parent::class . 'Validator';
 }
}
