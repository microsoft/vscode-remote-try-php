<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use ReflectionProperty;
interface TypedFieldMapper
{
 public function validateAndComplete(array $mapping, ReflectionProperty $field) : array;
}
