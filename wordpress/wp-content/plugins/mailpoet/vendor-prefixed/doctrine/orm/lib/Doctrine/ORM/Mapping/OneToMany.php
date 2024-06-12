<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class OneToMany implements Annotation
{
 public $mappedBy;
 public $targetEntity;
 public $cascade;
 public $fetch = 'LAZY';
 public $orphanRemoval = \false;
 public $indexBy;
 public function __construct(?string $mappedBy = null, ?string $targetEntity = null, ?array $cascade = null, string $fetch = 'LAZY', bool $orphanRemoval = \false, ?string $indexBy = null)
 {
 $this->mappedBy = $mappedBy;
 $this->targetEntity = $targetEntity;
 $this->cascade = $cascade;
 $this->fetch = $fetch;
 $this->orphanRemoval = $orphanRemoval;
 $this->indexBy = $indexBy;
 }
}
