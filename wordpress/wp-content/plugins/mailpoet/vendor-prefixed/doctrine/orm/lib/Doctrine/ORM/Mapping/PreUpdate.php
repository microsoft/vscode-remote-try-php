<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
#[\Attribute(Attribute::TARGET_METHOD)]
final class PreUpdate implements MappingAttribute
{
}
