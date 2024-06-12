<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
final class AttributeOverride implements Annotation
{
 public $name;
 public $column;
 public function __construct(string $name, Column $column)
 {
 $this->name = $name;
 $this->column = $column;
 }
}
