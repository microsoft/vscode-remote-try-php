<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
final class NamedQuery implements MappingAttribute
{
 public $name;
 public $query;
}
