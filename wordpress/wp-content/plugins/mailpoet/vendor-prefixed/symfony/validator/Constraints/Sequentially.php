<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Sequentially extends Composite
{
 public $constraints = [];
 public function __construct($constraints = null, array $groups = null, $payload = null)
 {
 parent::__construct($constraints ?? [], $groups, $payload);
 }
 public function getDefaultOption()
 {
 return 'constraints';
 }
 public function getRequiredOptions()
 {
 return ['constraints'];
 }
 protected function getCompositeOption()
 {
 return 'constraints';
 }
 public function getTargets()
 {
 return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
 }
}
