<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class SequenceGenerator implements MappingAttribute
{
 public $sequenceName;
 public $allocationSize = 1;
 public $initialValue = 1;
 public function __construct(?string $sequenceName = null, int $allocationSize = 1, int $initialValue = 1)
 {
 $this->sequenceName = $sequenceName;
 $this->allocationSize = $allocationSize;
 $this->initialValue = $initialValue;
 }
}
