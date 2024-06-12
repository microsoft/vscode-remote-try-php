<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class GeneratedValue implements Annotation
{
 public $strategy = 'AUTO';
 public function __construct(string $strategy = 'AUTO')
 {
 $this->strategy = $strategy;
 }
}
