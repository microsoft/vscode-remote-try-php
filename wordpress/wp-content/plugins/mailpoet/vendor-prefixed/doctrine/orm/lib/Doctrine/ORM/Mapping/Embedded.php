<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class Embedded implements Annotation
{
 public $class;
 public $columnPrefix;
 public function __construct(?string $class = null, $columnPrefix = null)
 {
 $this->class = $class;
 $this->columnPrefix = $columnPrefix;
 }
}
