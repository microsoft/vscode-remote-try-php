<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class OneToOne implements Annotation
{
 public $targetEntity;
 public $mappedBy;
 public $inversedBy;
 public $cascade;
 public $fetch = 'LAZY';
 public $orphanRemoval = \false;
 public function __construct(?string $mappedBy = null, ?string $inversedBy = null, ?string $targetEntity = null, ?array $cascade = null, string $fetch = 'LAZY', bool $orphanRemoval = \false)
 {
 $this->mappedBy = $mappedBy;
 $this->inversedBy = $inversedBy;
 $this->targetEntity = $targetEntity;
 $this->cascade = $cascade;
 $this->fetch = $fetch;
 $this->orphanRemoval = $orphanRemoval;
 }
}
