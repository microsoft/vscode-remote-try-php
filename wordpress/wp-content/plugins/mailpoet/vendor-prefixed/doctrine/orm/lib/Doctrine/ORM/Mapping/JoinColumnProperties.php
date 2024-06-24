<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
trait JoinColumnProperties
{
 public $name;
 public $referencedColumnName = 'id';
 public $unique = \false;
 public $nullable = \true;
 public $onDelete;
 public $columnDefinition;
 public $fieldName;
 public $options = [];
 public function __construct(?string $name = null, string $referencedColumnName = 'id', bool $unique = \false, bool $nullable = \true, $onDelete = null, ?string $columnDefinition = null, ?string $fieldName = null, array $options = [])
 {
 $this->name = $name;
 $this->referencedColumnName = $referencedColumnName;
 $this->unique = $unique;
 $this->nullable = $nullable;
 $this->onDelete = $onDelete;
 $this->columnDefinition = $columnDefinition;
 $this->fieldName = $fieldName;
 $this->options = $options;
 }
}
