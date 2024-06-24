<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Index implements MappingAttribute
{
 public $name;
 public $columns;
 public $fields;
 public $flags;
 public $options;
 public function __construct(?array $columns = null, ?array $fields = null, ?string $name = null, ?array $flags = null, ?array $options = null)
 {
 $this->columns = $columns;
 $this->fields = $fields;
 $this->name = $name;
 $this->flags = $flags;
 $this->options = $options;
 }
}
