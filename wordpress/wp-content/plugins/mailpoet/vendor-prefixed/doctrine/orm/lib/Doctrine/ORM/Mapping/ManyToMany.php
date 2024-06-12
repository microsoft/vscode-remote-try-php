<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class ManyToMany implements Annotation
{
 public $targetEntity;
 public $mappedBy;
 public $inversedBy;
 public $cascade;
 public $fetch = 'LAZY';
 public $orphanRemoval = \false;
 public $indexBy;
 public function __construct(?string $targetEntity = null, ?string $mappedBy = null, ?string $inversedBy = null, ?array $cascade = null, string $fetch = 'LAZY', bool $orphanRemoval = \false, ?string $indexBy = null)
 {
 if ($targetEntity === null) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8753', 'Passing no target entity is deprecated.');
 }
 $this->targetEntity = $targetEntity;
 $this->mappedBy = $mappedBy;
 $this->inversedBy = $inversedBy;
 $this->cascade = $cascade;
 $this->fetch = $fetch;
 $this->orphanRemoval = $orphanRemoval;
 $this->indexBy = $indexBy;
 }
}
