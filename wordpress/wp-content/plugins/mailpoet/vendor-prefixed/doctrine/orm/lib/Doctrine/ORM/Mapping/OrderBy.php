<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class OrderBy implements Annotation
{
 public $value;
 public function __construct(array $value)
 {
 $this->value = $value;
 }
}
