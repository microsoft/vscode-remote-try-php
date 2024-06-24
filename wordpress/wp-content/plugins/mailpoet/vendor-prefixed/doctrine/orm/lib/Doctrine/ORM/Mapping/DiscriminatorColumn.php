<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_CLASS)]
final class DiscriminatorColumn implements MappingAttribute
{
 public $name;
 public $type;
 public $length;
 public $columnDefinition;
 public $enumType = null;
 public function __construct(?string $name = null, ?string $type = null, ?int $length = null, ?string $columnDefinition = null, ?string $enumType = null)
 {
 $this->name = $name;
 $this->type = $type;
 $this->length = $length;
 $this->columnDefinition = $columnDefinition;
 $this->enumType = $enumType;
 }
}
