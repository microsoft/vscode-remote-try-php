<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class JoinColumn implements Annotation
{
 public $name;
 public $referencedColumnName = 'id';
 public $unique = \false;
 public $nullable = \true;
 public $onDelete;
 public $columnDefinition;
 public $fieldName;
 public function __construct(?string $name = null, string $referencedColumnName = 'id', bool $unique = \false, bool $nullable = \true, $onDelete = null, ?string $columnDefinition = null, ?string $fieldName = null)
 {
 $this->name = $name;
 $this->referencedColumnName = $referencedColumnName;
 $this->unique = $unique;
 $this->nullable = $nullable;
 $this->onDelete = $onDelete;
 $this->columnDefinition = $columnDefinition;
 $this->fieldName = $fieldName;
 }
}
