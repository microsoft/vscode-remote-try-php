<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use MailPoetVendor\Doctrine\ORM\EntityRepository;
#[\Attribute(Attribute::TARGET_CLASS)]
final class MappedSuperclass implements MappingAttribute
{
 public $repositoryClass;
 public function __construct(?string $repositoryClass = null)
 {
 $this->repositoryClass = $repositoryClass;
 }
}
