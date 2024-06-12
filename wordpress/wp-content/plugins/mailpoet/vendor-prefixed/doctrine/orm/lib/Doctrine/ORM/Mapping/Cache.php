<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
final class Cache implements Annotation
{
 public $usage = 'READ_ONLY';
 public $region;
 public function __construct(string $usage = 'READ_ONLY', ?string $region = null)
 {
 $this->usage = $usage;
 $this->region = $region;
 }
}
