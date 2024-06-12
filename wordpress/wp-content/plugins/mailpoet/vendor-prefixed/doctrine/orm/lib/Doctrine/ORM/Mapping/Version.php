<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
#[\Attribute(Attribute::TARGET_PROPERTY)]
final class Version implements Annotation
{
}
