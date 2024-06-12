<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
interface MetadataInterface
{
 public function getCascadingStrategy();
 public function getTraversalStrategy();
 public function getConstraints();
 public function findConstraints(string $group);
}
