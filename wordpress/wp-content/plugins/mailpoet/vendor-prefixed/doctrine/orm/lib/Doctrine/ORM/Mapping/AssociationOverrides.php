<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use Attribute;
use function array_values;
use function is_array;
#[\Attribute(Attribute::TARGET_CLASS)]
final class AssociationOverrides implements MappingAttribute
{
 public $overrides = [];
 public function __construct($overrides)
 {
 if (!is_array($overrides)) {
 $overrides = [$overrides];
 }
 foreach ($overrides as $override) {
 if (!$override instanceof AssociationOverride) {
 throw MappingException::invalidOverrideType('AssociationOverride', $override);
 }
 }
 $this->overrides = array_values($overrides);
 }
}
