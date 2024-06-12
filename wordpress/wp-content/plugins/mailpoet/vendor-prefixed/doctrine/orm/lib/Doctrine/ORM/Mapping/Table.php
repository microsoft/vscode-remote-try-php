<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_CLASS)]
final class Table implements Annotation
{
 public $name;
 public $schema;
 public $indexes;
 public $uniqueConstraints;
 public $options = [];
 public function __construct(?string $name = null, ?string $schema = null, ?array $indexes = null, ?array $uniqueConstraints = null, array $options = [])
 {
 $this->name = $name;
 $this->schema = $schema;
 $this->indexes = $indexes;
 $this->uniqueConstraints = $uniqueConstraints;
 $this->options = $options;
 }
}
