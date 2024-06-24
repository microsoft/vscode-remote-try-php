<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use ReflectionProperty;
final class ChainTypedFieldMapper implements TypedFieldMapper
{
 private array $typedFieldMappers;
 public function __construct(TypedFieldMapper ...$typedFieldMappers)
 {
 $this->typedFieldMappers = $typedFieldMappers;
 }
 public function validateAndComplete(array $mapping, ReflectionProperty $field) : array
 {
 foreach ($this->typedFieldMappers as $typedFieldMapper) {
 $mapping = $typedFieldMapper->validateAndComplete($mapping, $field);
 }
 return $mapping;
 }
}
