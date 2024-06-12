<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class ManyToOne implements Annotation
{
 public $targetEntity;
 public $cascade;
 public $fetch = 'LAZY';
 public $inversedBy;
 public function __construct(?string $targetEntity = null, ?array $cascade = null, string $fetch = 'LAZY', ?string $inversedBy = null)
 {
 $this->targetEntity = $targetEntity;
 $this->cascade = $cascade;
 $this->fetch = $fetch;
 $this->inversedBy = $inversedBy;
 }
}
