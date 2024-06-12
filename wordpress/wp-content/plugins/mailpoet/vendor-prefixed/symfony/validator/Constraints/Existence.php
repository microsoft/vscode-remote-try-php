<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
abstract class Existence extends Composite
{
 public $constraints = [];
 public function getDefaultOption()
 {
 return 'constraints';
 }
 protected function getCompositeOption()
 {
 return 'constraints';
 }
}
