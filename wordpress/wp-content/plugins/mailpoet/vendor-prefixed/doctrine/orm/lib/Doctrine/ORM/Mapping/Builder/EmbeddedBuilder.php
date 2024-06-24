<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
class EmbeddedBuilder
{
 private $builder;
 private $mapping;
 public function __construct(ClassMetadataBuilder $builder, array $mapping)
 {
 $this->builder = $builder;
 $this->mapping = $mapping;
 }
 public function setColumnPrefix($columnPrefix)
 {
 $this->mapping['columnPrefix'] = $columnPrefix;
 return $this;
 }
 public function build()
 {
 $cm = $this->builder->getClassMetadata();
 $cm->mapEmbedded($this->mapping);
 return $this->builder;
 }
}
