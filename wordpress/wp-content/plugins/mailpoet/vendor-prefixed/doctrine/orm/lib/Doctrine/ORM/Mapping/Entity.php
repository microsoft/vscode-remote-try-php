<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use MailPoetVendor\Doctrine\ORM\EntityRepository;
#[\Attribute(Attribute::TARGET_CLASS)]
final class Entity implements MappingAttribute
{
 public $repositoryClass;
 public $readOnly = \false;
 public function __construct(?string $repositoryClass = null, bool $readOnly = \false)
 {
 $this->repositoryClass = $repositoryClass;
 $this->readOnly = $readOnly;
 }
}
