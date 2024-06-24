<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
class ClassMetadata extends ClassMetadataInfo
{
 public function __construct($entityName, ?NamingStrategy $namingStrategy = null, ?TypedFieldMapper $typedFieldMapper = null)
 {
 parent::__construct($entityName, $namingStrategy, $typedFieldMapper);
 }
}
