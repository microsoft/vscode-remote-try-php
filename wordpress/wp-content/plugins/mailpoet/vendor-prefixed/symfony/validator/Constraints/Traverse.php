<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
#[\Attribute(\Attribute::TARGET_CLASS)]
class Traverse extends Constraint
{
 public $traverse = \true;
 public function __construct($traverse = null)
 {
 if (\is_array($traverse) && \array_key_exists('groups', $traverse)) {
 throw new ConstraintDefinitionException(\sprintf('The option "groups" is not supported by the constraint "%s".', __CLASS__));
 }
 parent::__construct($traverse);
 }
 public function getDefaultOption()
 {
 return 'traverse';
 }
 public function getTargets()
 {
 return self::CLASS_CONSTRAINT;
 }
}
