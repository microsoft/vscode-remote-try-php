<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use BackedEnum;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class Column implements MappingAttribute
{
 public $name;
 public $type;
 public $length;
 public $precision = 0;
 public $scale = 0;
 public $unique = \false;
 public $nullable = \false;
 public $insertable = \true;
 public $updatable = \true;
 public $enumType = null;
 public $options = [];
 public $columnDefinition;
 public $generated;
 public function __construct(?string $name = null, ?string $type = null, ?int $length = null, ?int $precision = null, ?int $scale = null, bool $unique = \false, bool $nullable = \false, bool $insertable = \true, bool $updatable = \true, ?string $enumType = null, array $options = [], ?string $columnDefinition = null, ?string $generated = null)
 {
 $this->name = $name;
 $this->type = $type;
 $this->length = $length;
 $this->precision = $precision;
 $this->scale = $scale;
 $this->unique = $unique;
 $this->nullable = $nullable;
 $this->insertable = $insertable;
 $this->updatable = $updatable;
 $this->enumType = $enumType;
 $this->options = $options;
 $this->columnDefinition = $columnDefinition;
 $this->generated = $generated;
 }
}
