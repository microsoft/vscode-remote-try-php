<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class UniqueConstraint implements MappingAttribute
{
 public $name;
 public $columns;
 public $fields;
 public $options;
 public function __construct(?string $name = null, ?array $columns = null, ?array $fields = null, ?array $options = null)
 {
 $this->name = $name;
 $this->columns = $columns;
 $this->fields = $fields;
 $this->options = $options;
 }
}
