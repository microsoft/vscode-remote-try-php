<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
#[\Attribute(\Attribute::TARGET_CLASS)]
class GroupSequence
{
 public $groups;
 public $cascadedGroup;
 public function __construct(array $groups)
 {
 // Support for Doctrine annotations
 $this->groups = $groups['value'] ?? $groups;
 }
}
